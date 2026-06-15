<?php

namespace App\Imports;

use Illuminate\Support\Facades\{DB, Log, Storage, Validator};
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Carbon\Carbon;
use App\Models\{
    Shelter,
    TenantModel,
    BranchModel,
    Amenities,
    LocationModel,
    Shelter_Amenities,
    ApartmentIdentity,
    EstateOwner,
    AmenitySize
};

class SingleSheetImport implements ToArray
{
    private string $sheetName;

    public function __construct(string $sheetName)
    {
    
        $this->sheetName = strtolower(trim($sheetName));
    }

    public function array(array $rows): void
    {
        Log::info("Processing sheet context: {$this->sheetName}");

        if (empty($rows)) {
            Log::warning("Sheet '{$this->sheetName}' contains no structural rows.");
            return;
        }

        // Shift the first row out to treat it as the header
        $rawHeader = array_shift($rows);
    
        if (!$rawHeader) {
            Log::warning("Sheet '{$this->sheetName}' has missing header layout.");
            return;
        }
        $header = array_map('trim', $rawHeader);
        // Sanitize collection array, ignoring completely blank spacer rows
        $validRows = [];
        foreach ($rows as $row) {
            if (!$this->isRowEmpty($row)) {
                $validRows[] = $row;
            }
        }
        if (empty($validRows)) {
            Log::info("Sheet '{$this->sheetName}' contains empty cells only. Skipping execution.");
            return;
        }

        // Preload relational reference keys to optimize against N+1 query overheads
        $shelters  = Shelter::select('id', 'name')->get()->keyBy(fn($s) => strtolower(trim($s->name)));
        $branches  = BranchModel::select('id', 'name')->get()->keyBy(fn($b) => strtolower(trim($b->name)));
        $locations = LocationModel::select('id', 'name')->get()->keyBy(fn($l) => strtolower(trim($l->name)));
        $landlords = EstateOwner::select('id', 'email', 'phones')->get()->keyBy(fn($l) => strtolower(trim($l->email)));
        $amenities = Amenities::all();

        // Router routing explicitly targeted per sheet namespace context
        switch ($this->sheetName) {
            case 'tenants':
                $this->processTenants($header, $validRows);
                break;
            case 'apartments':
                $this->processApartments($header, $validRows, $shelters, $branches, $locations, $landlords, $amenities);
                break;
            case 'shelter types':
                $this->processShelter($header, $validRows);
                break;
            case 'branch':
                $this->processBranches($header, $validRows);
                break;
            case 'location':
                $this->processLocations($header, $validRows, $branches);
                break;
            default:
                Log::warning("Unknown operational layout profile handled: {$this->sheetName}");
                break;
        }
    }

    // ====================== SHELTER TYPES ======================
    private function processShelter(array $header, array $rows): void
    {
        foreach ($rows as $row) {
            if (!is_array($row) || count($header) !== count($row)) continue;

            $data = array_combine($header, $row);
            $shelterNameRaw = $data['Name'] ?? $data['name'] ?? null;

            if (empty($shelterNameRaw)) continue;

            $shelterName = Str::singular(strtolower(trim($shelterNameRaw)));

            if (!Shelter::whereRaw('LOWER(name) = ?', [$shelterName])->exists()) {
                Shelter::create([
                    'name'      => $shelterName,
                    'is_active' => true,
                ]);
                Log::info("Created shelter type: {$shelterName}");
            }
        }
    }

    // ====================== BRANCHES ======================
    private function processBranches(array $header, array $rows): void
    {
        foreach ($rows as $row) {
            if (!is_array($row) || count($header) !== count($row)) continue;

            $data = array_combine($header, $row);
            $name = trim($data['Name'] ?? $data['name'] ?? '');
            $address = trim($data['Address'] ?? $data['address'] ?? '');

            if (empty($name)) continue;

            BranchModel::updateOrCreate(
                ['name' => $name],
                ['name' => $name, 'address' => $address]
            );
        }
    }

    // ====================== LOCATIONS ======================
    private function processLocations(array $header, array $rows, $branches): void
    {
        foreach ($rows as $row) {
            if (!is_array($row) || count($header) !== count($row)) continue;

            $data = array_combine($header, $row);
            $name = trim($data['Name'] ?? $data['name'] ?? '');
            $branchName = trim($data['Branch'] ?? $data['branch'] ?? '');

            if (empty($name)) continue;

            $branchId = $branches[strtolower($branchName)]->id ?? null;

            LocationModel::updateOrCreate(
                ['name' => $name],
                [
                    'name' => $name,
                    'branch_id' => $branchId,
                ]
            );
        }
    }

    // ====================== TENANTS ======================
    private function processTenants(array $header, array $rows): void
    {
        foreach ($rows as $rowIndex => $row) {
            if (!is_array($row) || count($header) !== count($row)) continue;

            $data = array_combine($header, $row);

            $tenantData = [
                'full_name'           => trim($data['Full Name'] ?? ''),
                'date_of_birth'       => $this->parseDate($data['Date of Birth'] ?? null),
                'gender'              => strtolower(trim($data['Gender'] ?? '')),
                'nationality'         => trim($data['Nationality'] ?? ''),
                'state'               => trim($data['State'] ?? ''),
                'address'             => trim($data['Address'] ?? ''),
                'id_method'           => strtolower(trim($data['ID Method'] ?? '')),
                'mobile_number'       => trim($data['Mobile Number'] ?? ''),
                'home_number'         => trim($data['Home Number'] ?? ''),
                'occupant_email'      => trim($data['Email'] ?? ''),
                'emergency_contact'   => trim($data['Emergency Contact'] ?? ''),
                'emergency_email'     => trim($data['Emergency Email'] ?? ''),
                'guarantor_full_name' => trim($data['Guarantor Full Name'] ?? ''),
                'guarantor_address'   => trim($data['Guarantor Address'] ?? ''),
                'guarantor_phone'     => trim($data['Guarantor Phone'] ?? ''),
                'guarantor_email'     => trim($data['Guarantor Email'] ?? ''),
            ];

            $validator = Validator::make($tenantData, [
                'full_name'     => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'gender'        => 'required|in:male,female,other',
                'mobile_number' => 'required|unique:tenants,mobile_number',
                'occupant_email'=> 'nullable|email',
            ]);

            if ($validator->fails()) {
                Log::warning("Tenant validation failed at row indexing identifier {$rowIndex}", $validator->errors()->toArray());
                continue;
            }

            $tenantData['identification_image'] = $this->storePrivateImage($data['Identification Image'] ?? '', 'identification_images');
            $tenantData['passport_photograph']  = $this->storePrivateImage($data['Passport Photograph'] ?? '', 'passport_photographs');

            TenantModel::create($tenantData);
        }
    }

    // ====================== APARTMENTS ======================
    private function processApartments(array $header, array $rows, $shelters, $branches, $locations, $landlords, $amenities): void
    {
        foreach ($rows as $rowIndex => $row) {
            if (!is_array($row) || count($header) !== count($row)) continue;

            $data = array_combine($header, $row);

            try {
                $shelterName   = strtolower(trim($data['Shelter Type'] ?? ''));
                $locationName  = trim($data['Location'] ?? '');
                $branchName    = trim($data['Branch'] ?? '');
                $landlordEmail = strtolower(trim($data['Landlord Email'] ?? $data['email'] ?? ''));

                $shelter  = $shelters[$shelterName] ?? null;
                $location = $locations[strtolower($locationName)] ?? null;
                $branch   = $branches[strtolower($branchName)] ?? null;
                $landlord = $landlords[$landlordEmail] ?? null;

                if (!$shelter || !$location || !$branch || !$landlord) {
                    throw new \Exception("Missing baseline relative reference key maps for row indicator: {$rowIndex}");
                }

                $unitNumber = trim($data['Unit number'] ?? '');
                $address    = strtolower(trim($data['Address'] ?? ''));

                if (ApartmentIdentity::where([
                    'branch_id'          => $branch->id,
                    'location_models_id' => $location->id,
                    'shelter_id'         => $shelter->id,
                    'unit_number'        => $unitNumber,
                ])->exists()) {
                    Log::warning("Duplicate structural unit record bypassed at row element indexing: {$rowIndex}");
                    continue;
                }

                $apartmentUtil = new ApartmentIdentity();
                $uniqueCode    = $apartmentUtil->generateUniqueCode($branch->id, $location->id);

                $apartment = ApartmentIdentity::create([
                    'branch_id'          => $branch->id,
                    'location_models_id' => $location->id,
                    'shelter_id'         => $shelter->id,
                    'landlord_id'        => $landlord->id,
                    'address'            => $address,
                    'unit_number'        => $unitNumber,
                    'unique_code'        => $uniqueCode,
                ]);

                foreach ($amenities as $amenity) {
                    $value = (int)($data[$amenity->name] ?? 0);

                    Shelter_Amenities::updateOrCreate(
                        ['id_apartment_id' => $apartment->id, 'amenity_id' => $amenity->id],
                        ['amenity_number' => $value, 'branch_id' => $branch->id]
                    );

                    AmenitySize::updateOrCreate(
                        ['apartment_id' => $apartment->id, 'amenity_id' => $amenity->id],
                        ['amenity_name' => $amenity->name, 'amenity_size' => $value]
                    );
                }

            } catch (\Throwable $e) {
                Log::error("Apartment item storage exception thrown at indexing row link {$rowIndex}: " . $e->getMessage());
            }
        }
    }

    // ====================== HELPERS ======================
    private function parseDate($value): ?string
    {
        if (empty($value)) return null;
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Unmatched explicit date sequence context: {$value}");
            return null;
        }
    }

    private function storePrivateImage(string $sourceFile, string $directory): ?string
    {
        $sourceFile = trim($sourceFile);
        if (empty($sourceFile) || !file_exists($sourceFile)) {
            return null;
        }

        $extension = strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return null;
        }

        $newFilename = Str::uuid() . '.' . $extension;
        $storagePath = $directory . '/' . $newFilename;

        Storage::disk('private')->put($storagePath, file_get_contents($sourceFile));

        return $storagePath;
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            $value = trim((string) $cell);
            if ($value !== '') {
                return false;
            }
        }
        return true;
    }
}