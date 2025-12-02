<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use App\Models\Shelter;
use App\Models\ApartmentIdentity;
use App\Models\TenancyTypeModel;
use App\Models\Amenities;
use App\Models\AmenitySize;
use App\Models\BlockModel;
use App\Models\Block_Shelter;
use App\Models\Shelter_Amenities;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MultiTableImport implements ToArray
{
    private const DEFAULT_BLOCK_ID = 1;
    private const DEFAULT_SHELTER_ID_FLAT = 3;

    /**
     * Process the Excel array data
     */
    public function array(array $array)
    {
        if (empty($array)) {
            Log::warning('Empty Excel file provided');
            return;
        }

        $header = array_shift($array);
        $shelterTypes = Shelter::pluck('name', 'id')->map(fn($name) => strtolower($name));
        $tenancyTypes = TenancyTypeModel::pluck('name')->map(fn($name) => strtolower($name));
        $bedAmenity = Amenities::where('name', 'bed')->select('id')->first();

        if (!$bedAmenity) {
            Log::error('Bed amenity not found in database');
            return;
        }

        DB::beginTransaction();

        try {
            $blockData = [];

            foreach ($array as $row) {
                $rowAssoc = array_combine($header, $row);
                $this->processRow($rowAssoc, $shelterTypes, $tenancyTypes, $bedAmenity->id, $blockData);
            }

            Log::info('Number of unique blocks processed: ' . count($blockData));
            Log::info('Number of apartments processed: ' . array_sum($blockData));
            Log::info('Block details: ' . json_encode($blockData));

            foreach ($blockData as $blockName => $apartmentCount) {
                $this->updateShelterQty($blockName, $apartmentCount);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function processRow(array $rowAssoc, Collection $shelterTypes, Collection $tenancyTypes, int $bedAmenityId, array &$blockData): void
    {
        $address = strtolower(trim($rowAssoc['ADDRESS'] ?? ''));
        $data = [
            'admin_unit' => $rowAssoc['ADMIN_UNIT'] ?? null,
            'unit_name' => $rowAssoc['UNIT NAME'] ?? null,
            'property_ref' => $rowAssoc['PROPERTY REF'] ?? null,
            'address' => $address,
            'postcode' => $rowAssoc['POSTCODE'] ?? null,
            'ownership' => $rowAssoc['OWNERSHIP'] ?? null,
            'pro_sco_code' => $rowAssoc['PRO_SCO_CODE'] ?? null,
            'tenancy_type' => strtolower($rowAssoc['TENANCY TYPE'] ?? ''),
            'beds' => [
                $rowAssoc['BED 1 SIZE'] ?? null,
                $rowAssoc['BED 2 SIZE'] ?? null,
                $rowAssoc['BED 3 SIZE'] ?? null,
                $rowAssoc['BED 4 SIZE'] ?? null,
                $rowAssoc['BED 5 SIZE'] ?? null,
            ],
        ];

        [$houseNumber, $blockName] = $this->parseAddress($address);
        $blockName = strtolower($blockName ?? 'unknown');
        $shelterId = $this->findShelterId($address, $shelterTypes) ?? self::DEFAULT_SHELTER_ID_FLAT;

        if ($data['tenancy_type'] && !$tenancyTypes->contains($data['tenancy_type'])) {
            TenancyTypeModel::create(['name' => $data['tenancy_type']]);
            $tenancyTypes->push($data['tenancy_type']);
        }

        // Get or create block and block_shelter first
        $blockModel = BlockModel::firstOrCreate(
            ['name' => $blockName],
            ['country_name' => 'n/a', 'landlord_id' => 1]
        );

        $blockShelter = Block_Shelter::firstOrCreate(
            [
                'block_models_id' => $blockModel->id,
                'shelter_id' => $shelterId,
            ],
            ['shelter_qty' => 0, 'estate_owner_id' => 1]
        );

        $apartment = $this->upsertApartment($data, $shelterId, $houseNumber ?? $data['unit_name'], $blockName, $blockShelter->id);
        $this->upsertShelterAmenities($blockModel->id, $apartment->id, $shelterId, $bedAmenityId, count(array_filter($data['beds'])), $blockShelter->id);
        $this->upsertBedAmenities($apartment->id, $data['beds'], $bedAmenityId);

        $blockData[$blockName] = ($blockData[$blockName] ?? 0) + 1;
    }

    private function parseAddress(string $address): array
    {
        $patterns = [
            '/(\d+)\s*,\s*([\w\s]+)\s*,/',
            '/(\d+)\s+([\w\s]+),\s*[\w\s]+,\s*[a-zA-Z0-9\s]+/',
            '/(\d+)\s+([\w\s]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $address, $matches)) {
                return [$matches[1], $matches[2]];
            }
        }

        Log::warning('Unable to parse address: ' . $address);
        return [null, 'unknown'];
    }

    private function findShelterId(string $address, Collection $shelterTypes): ?int
    {
        foreach ($shelterTypes as $id => $name) {
            if (str_contains(strtolower($address), $name)) {
                return $id;
            }
        }
        Log::warning("No matching shelter found for address: {$address}");
        return null;
    }

    private function upsertApartment(array $data, int $shelterId, ?string $unitNumber, string $blockName, int $blockShelterId): ApartmentIdentity
    {
        $blockModel = BlockModel::firstOrCreate(
            ['name' => $blockName],
            ['country_name' => 'n/a', 'landlord_id' => 1]
        );
        $uniqueCode = (new ApartmentIdentity())->generateUniqueCode($blockModel->id, $shelterId);

        return ApartmentIdentity::updateOrCreate(
            [
                'property_ref' => $data['property_ref'],
                'pro_sco_code' => $data['pro_sco_code'],
            ],
            [
                'shelter_id' => $shelterId,
                'block_models_id' => $blockModel->id,
                'block_shelter_id' => $blockShelterId, // Use dynamic block_shelter_id
                'tenancy_type' => $data['tenancy_type'],
                'admin_unit' => $data['admin_unit'],
                'unit_number' => $unitNumber,
                'address' => $data['address'],
                'post_code' => $data['postcode'],
                'ownership' => $data['ownership'],
                'unique_code' => $uniqueCode,
            ]
        );
    }

    private function upsertBedAmenities(int $apartmentId, array $beds, int $bedAmenityId): void
    {
        foreach (array_filter($beds) as $index => $size) {
            AmenitySize::updateOrCreate(
                [
                    'apartment_id' => $apartmentId,
                    'amenity_id' => $bedAmenityId,
                    'amenity_name' => 'bed',
                    'amenity_size' => $size,
                ],
                [
                    'block_models_id' => self::DEFAULT_BLOCK_ID,
                    'shelter_id' => self::DEFAULT_SHELTER_ID_FLAT,
                ]
            );
        }
    }

    private function updateShelterQty($blockName, $apartmentCount): void
    {
        $blockModel = BlockModel::firstOrCreate(
            ['name' => $blockName],
            ['landlord_id' => 1]
        );

        Block_Shelter::updateOrCreate(
            [
                'block_models_id' => $blockModel->id,
                'shelter_id' => self::DEFAULT_SHELTER_ID_FLAT,
            ],
            ['shelter_qty' => $apartmentCount, 'estate_owner_id' => 1]
        );
    }

    private function upsertShelterAmenities(int $blockId, int $apartmentId, int $shelterId, int $amenityId, int $amenityNumber, int $blockShelterId): void
    {
        Shelter_Amenities::updateOrCreate(
            [
                'block_models_id' => $blockId,
                'id_apartment_id' => $apartmentId,
                'amenity_id' => $amenityId,
            ],
            [
                'amenity_number' => $amenityNumber,
                'block_shelter_id' => $blockShelterId, // Use dynamic block_shelter_id
            ]
        );
    }
}