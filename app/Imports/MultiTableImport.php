<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\{
    Shelter,
    LocationModel,
    ApartmentIdentity,
    TenancyTypeModel,
    Amenities,
    BlockModel,
    TenantModel,
    Block_Shelter,
    Shelter_Amenities
};

class MultiTableImport implements ToArray
{
    private const DEFAULT_SHELTER_ID_FLAT = 3;

    private array $locationCache = [];
    private array $blockCache = [];
    private array $blockShelterCache = [];
    private array $blockShelterCounts = [];
    private array $tenancyCache = [];

    public function array(array $rows): void
    {
        if (empty($rows)) {
            Log::warning('Empty Excel file');
            return;
        }

        $header = array_map('trim', array_shift($rows));

        $shelterTypes = Shelter::pluck('name', 'id')
            ->map(fn ($v) => strtolower($v))
            ->toArray();

        $this->tenancyCache = TenancyTypeModel::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->toArray();

        $bedAmenityId = Amenities::where('name', 'bed')->value('id');

        if (!$bedAmenityId) {
            Log::error('Bed amenity missing');
            return;
        }

        DB::transaction(function () use ($rows, $header, $shelterTypes, $bedAmenityId) {
            foreach ($rows as $row) {
                if (count($row) !== count($header)) {
                    continue;
                }

                $row = array_combine($header, $row);

                if (empty($row['PROPERTY REF'])) {
                    continue;
                }

                $this->processRow($row, $shelterTypes, $bedAmenityId);
            }

            foreach ($this->blockShelterCounts as $id => $count) {
                Block_Shelter::whereKey($id)->update(['shelter_qty' => $count]);
            }
        });
    }

    private function processRow(array $row, array $shelterTypes, int $bedAmenityId): void
    {
        $address = strtolower(trim($row['ADDRESS'] ?? ''));
        $locationName = strtolower(trim($row['Area Name'] ?? 'n/a'));
        $blockName = strtolower(trim($row['UNIT NAME'] ?? 'unknown'));
        $tenancy = strtolower(trim($row['TENANCY TYPE'] ?? ''));
        $bedrooms = (int) ($row['BEDROOMS'] ?? 0);

        $shelterId = $this->detectShelter($address, $shelterTypes);

        $tenancyId = $this->getTenancyId($tenancy);
        $locationId = $this->getLocationId($locationName);
        $blockId = $this->getBlockId($blockName, $locationId);
        $blockShelterId = $this->getBlockShelterId($blockId, $shelterId);
        $uniqueCode = (new ApartmentIdentity())->generateUniqueCode($blockId, $shelterId);

        $apartment = ApartmentIdentity::updateOrCreate(
            [
                'property_ref' => $row['PROPERTY REF'],
                'pro_sco_code' => $row['PRO_SCO_CODE'] ?? null,
            ],
            [
                'shelter_id' => $shelterId,
                'block_models_id' => $blockId,
                'block_shelter_id' => $blockShelterId,
                'tenancy_type' => $tenancy,
                'admin_unit' => $row['ADMIN_UNIT'] ?? null,
                'unit_number' => $row['Flat_Number'] ?? null,
                'address' => $address,
                'post_code' => $row['POSTCODE'] ?? null,
                'ownership' => $row['OWNERSHIP'] ?? null,
                'unique_code'=>$uniqueCode
            ]
        );

        Shelter_Amenities::updateOrCreate(
            [
                'block_models_id' => $blockId,
                'id_apartment_id' => $apartment->id,
                'amenity_id' => $bedAmenityId,
            ],
            [
                'amenity_number' => $bedrooms,
                'block_shelter_id' => $blockShelterId,
            ]
        );

        if (!empty($row['TENANT'])) {
            TenantModel::updateOrCreate(
                [
                    'full_name' => trim($row['TENANT']),
                ]
            );
        }

        $this->blockShelterCounts[$blockShelterId] =
            ($this->blockShelterCounts[$blockShelterId] ?? 0) + 1;
    }

    private function detectShelter(string $address, array $types): int
    {
        foreach ($types as $id => $name) {
            if (str_contains($address, $name)) {
                return (int) $id;
            }
        }
        return self::DEFAULT_SHELTER_ID_FLAT;
    }

    private function getTenancyId(string $name): ?int
    {
        if (!$name) return null;

        if (!isset($this->tenancyCache[$name])) {
            $this->tenancyCache[$name] =
                TenancyTypeModel::insertGetId(['name' => $name]);
        }

        return $this->tenancyCache[$name];
    }

    private function getLocationId(string $name): int
    {
        return $this->locationCache[$name]
            ??= LocationModel::firstOrCreate(['name' => $name])->id;
    }

    private function getBlockId(string $name, int $locationId): int
    {
        return $this->blockCache[$name]
            ??= BlockModel::firstOrCreate(
                ['name' => $name],
                ['location_id' => $locationId, 'landlord_id' => 1]
            )->id;
    }

    private function getBlockShelterId(int $blockId, int $shelterId): int
    {
        $key = "{$blockId}|{$shelterId}";

        return $this->blockShelterCache[$key]
            ??= Block_Shelter::firstOrCreate(
                ['block_models_id' => $blockId, 'shelter_id' => $shelterId],
                ['estate_owner_id' => 1, 'shelter_qty'=>1]
            )->id;
    }
}
