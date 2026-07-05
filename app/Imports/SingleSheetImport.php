<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\{Log, Storage, Validator};
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

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
            Log::warning("Sheet '{$this->sheetName}' contains no rows.");

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Read Header
        |--------------------------------------------------------------------------
        */

        $rawHeader = array_shift($rows);

        if (empty($rawHeader)) {
            Log::warning("Sheet '{$this->sheetName}' has no header row.");

            return;
        }

        $header = array_map(
            fn ($header) => $this->normalizeHeader($header),
            $rawHeader
        );
        

        /*
        |--------------------------------------------------------------------------
        | Remove completely empty rows
        |--------------------------------------------------------------------------
        */

        $validRows = [];

        foreach ($rows as $row) {

            if (!$this->isRowEmpty($row)) {
                $validRows[] = $row;
            }
        }

        if (empty($validRows)) {

            Log::info("Sheet '{$this->sheetName}' contains only empty rows.");

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Preload lookups
        |--------------------------------------------------------------------------
        */

        $shelters = Shelter::select('id', 'name')
            ->get()
            ->keyBy(fn ($item) => $this->normalizeLookup($item->name));

        $branches = BranchModel::select('id', 'name')
            ->get()
            ->keyBy(fn ($item) => $this->normalizeLookup($item->name));

        $locations = LocationModel::select('id', 'name')
            ->get()
            ->keyBy(fn ($item) => $this->normalizeLookup($item->name));

        /*
        |--------------------------------------------------------------------------
        | Landlord lookup
        |--------------------------------------------------------------------------
        |
        | Index landlords by BOTH:
        | - email
        | - every phone number
        |
        */

        $landlords = collect();

        EstateOwner::select(
            'id',
            'email',
            'phones'
        )
        ->get()
        ->each(function ($owner) use ($landlords) {

            if (!empty($owner->email)) {

                $landlords->put(
                    strtolower(trim($owner->email)),
                    $owner
                );
            }

            foreach (explode(',', $owner->phones ?? '') as $phone) {

                $phone = $this->normalizePhone($phone);

                if ($phone !== '') {
                    $landlords->put($phone, $owner);
                }
            }
        });

        $amenities = Amenities::all();

        /*
        |--------------------------------------------------------------------------
        | Route by sheet
        |--------------------------------------------------------------------------
        */

        switch ($this->sheetName) {
            

            case 'tenants':

                $this->processTenants(
                    $header,
                    $validRows
                );

                break;

            case 'apartments':

                $this->processApartments(
                    $header,
                    $validRows,
                    $shelters,
                    $branches,
                    $locations,
                    $landlords,
                    $amenities
                );

                break;

            case 'shelter types':

                $this->processShelter(
                    $header,
                    $validRows
                );

                break;

            case 'branch':

                $this->processBranches(
                    $header,
                    $validRows
                );

                break;

            case 'location':

                $this->processLocations(
                    $header,
                    $validRows,
                    $branches
                );

                break;

            default:

                Log::warning(
                    "Unknown sheet encountered: {$this->sheetName}"
                );

                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Convert Excel headers into a canonical form.
     *
     * Examples:
     *
     * FULL NAME
     * Full Name
     * full_name
     * Full-Name
     * full  name
     *
     * become
     *
     * full name
     */
    private function normalizeHeader(?string $header): string
    {
        $header = strtolower(trim((string) $header));

        $header = preg_replace('/[_\-]+/', ' ', $header);

        $header = preg_replace('/\s+/', ' ', $header);

        return trim($header);
    }

    /**
     * Normalize lookup values.
     */
    private function normalizeLookup(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    /**
     * Remove every non-digit from phone numbers.
     */
    private function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    /**
     * Build row using normalized headers.
     */
    private function mapRow(
        array $header,
        array $row
    ): array {

        if (count($header) !== count($row)) {

            return [];
        }

        return array_combine(
            $header,
            $row
        );
    }

    /**
     * Read a value using
     * case-insensitive normalized headers.
     */
    private function value(
        array $data,
        string ...$keys
    ): mixed {

        foreach ($keys as $key) {

            $key = $this->normalizeHeader($key);

            if (array_key_exists($key, $data)) {

                return is_string($data[$key])
                    ? trim($data[$key])
                    : $data[$key];
            }
        }

        return null;
    }
    private function processShelter(array $header, array $rows): void
{
    foreach ($rows as $rowIndex => $row) {

        $data = $this->mapRow($header, $row);

        if (empty($data)) {
            Log::warning("Invalid shelter row at index {$rowIndex}.");
            continue;
        }

        $shelterName = $this->value($data, 'name');

        if (empty($shelterName)) {
            continue;
        }

        $shelterName = Str::singular(
            strtolower(trim($shelterName))
        );

        Shelter::firstOrCreate(
            [
                'name' => $shelterName
            ],
            [
                'is_active' => true
            ]
        );
    }
}
private function processBranches(array $header, array $rows): void
{
    foreach ($rows as $rowIndex => $row) {

        $data = $this->mapRow($header, $row);

        if (empty($data)) {
            Log::warning("Invalid branch row at index {$rowIndex}.");
            continue;
        }

        $name = $this->value($data, 'name');
        $address = $this->value($data, 'address');

        if (empty($name)) {
            continue;
        }

        BranchModel::updateOrCreate(
            [
                'name' => trim($name)
            ],
            [
                'name' => trim($name),
                'address' => trim($address)
            ]
        );
    }
}
private function processLocations(
    array $header,
    array $rows,
    $branches
): void {

    foreach ($rows as $rowIndex => $row) {

        $data = $this->mapRow($header, $row);

        if (empty($data)) {
            Log::warning("Invalid location row at index {$rowIndex}.");
            continue;
        }

        $name = $this->value($data, 'name');
        $branchName = $this->value($data, 'branch');

        if (empty($name)) {
            continue;
        }

        $branch = $branches->get(
            $this->normalizeLookup($branchName)
        );

        LocationModel::updateOrCreate(
            [
                'name' => trim($name)
            ],
            [
                'name' => trim($name),
                'branch_id' => $branch?->id
            ]
        );
    }
}
private function processTenants(
    array $header,
    array $rows
): void {

    foreach ($rows as $rowIndex => $row) {


        $data = $this->mapRow($header, $row);
         Log::info('Header Count', ['count' => count($header), 'header' => $header]);

Log::info('Row Count', ['count' => count($row), 'row' => $row]);


        if (empty($data)) {

            Log::warning(
                "Invalid tenant row at index {$rowIndex}."
            );

            continue;
        }

        $tenantData = [

            'full_name' => $this->value(
                $data,
                'full name'
            ),

            'date_of_birth' => $this->parseDate(
                $this->value(
                    $data,
                    'date of birth'
                )
            ),

            'gender' => strtolower(
                (string) $this->value(
                    $data,
                    'gender'
                )
            ),

            'nationality' => $this->value(
                $data,
                'nationality'
            ),

            'state' => $this->value(
                $data,
                'state'
            ),

            'address' => $this->value(
                $data,
                'address'
            ),

           'id_method' => $this->normalizeIdentificationMethod(
    $this->value(
        $data,
        'id method',
        'identification method'
    )
),

            'mobile_number' => $this->normalizePhone(
                $this->value(
                    $data,
                    'mobile number'
                )
            ),

            'home_number' => $this->normalizePhone(
                $this->value(
                    $data,
                    'home number'
                )
            ),

            'occupant_email' => strtolower(
                (string) $this->value(
                    $data,
                    'email'
                )
            ),

            'emergency_contact' => $this->value(
                $data,
                'emergency contact'
            ),

            'emergency_email' => strtolower(
                (string) $this->value(
                    $data,
                    'emergency email'
                )
            ),

   'guarantor_full_name' => $this->value(
    $data,
    'guarantor full name',
    'gurantor full name'
),

'guarantor_address' => $this->value(
    $data,
    'guarantor address',
    'gurantor address'
),

'guarantor_email' => $this->value(
    $data,
    'guarantor email',
    'gurantor email'
),

'guarantor_phone' => $this->value(
    $data,
    'guarantor phone',
    'gurantor phone'
),

        ];

        $validator = Validator::make($tenantData, [

            'full_name' => 'required|string|max:255',

            'date_of_birth' => 'nullable|date',

            'gender' => 'required|in:male,female,other',

            'mobile_number' => 'required|unique:tenants,mobile_number',

            'occupant_email' => 'nullable|email',
            'id_method' => 'nullable|in:driver_licence,nin,nis,passport',

        ]);

        if ($validator->fails()) {

            Log::warning(
                "Tenant validation failed on row {$rowIndex}",
                $validator->errors()->toArray()
            );

            continue;
        }

        $tenantData['identification_image'] = $this->storePrivateImage(

            (string) $this->value(
                $data,
                'identification image'
            ),

            'identification_images'

        );

        $tenantData['passport_photograph'] = $this->storePrivateImage(

            (string) $this->value(
                $data,
                'passport photograph'
            ),

            'passport_photographs'

        );

        TenantModel::create($tenantData);
    }
}
private function processApartments(
    array $header,
    array $rows,
    $shelters,
    $branches,
    $locations,
    $landlords,
    $amenities
): void {

    foreach ($rows as $rowIndex => $row) {

        $data = $this->mapRow($header, $row);
       
        if (empty($data)) {

            Log::warning(
                "Invalid apartment row at index {$rowIndex}."
            );

            continue;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Resolve Relationships
            |--------------------------------------------------------------------------
            */

            $shelterName = $this->normalizeLookup(
                $this->value($data, 'shelter type')
            );

            $branchName = $this->normalizeLookup(
                $this->value($data, 'branch')
            );

            $locationName = $this->normalizeLookup(
                $this->value($data, 'location')
            );

            $landlordEmail = strtolower(trim(
                (string) $this->value(
                    $data,
                    'estate owner email',
                    'email'
                )
            ));

            $landlordPhone = $this->normalizePhone(
                $this->value(
                    $data,
                    'estate owner phone',
                    'estate owner phones',
                    'phone'
                )
            );

            $shelter = $shelters->get($shelterName);

            $branch = $branches->get($branchName);

            $location = $locations->get($locationName);

            $landlord = null;

            if (!empty($landlordEmail)) {

                $landlord = $landlords->get($landlordEmail);
            }

            if (!$landlord && !empty($landlordPhone)) {

                $landlord = $landlords->get($landlordPhone);
            }

            /*
            |--------------------------------------------------------------------------
            | Detailed Missing References
            |--------------------------------------------------------------------------
            */

            $missing = [];

            if (!$shelter) {
                $missing[] = "Shelter Type '{$shelterName}'";
            }

            if (!$branch) {
                $missing[] = "Branch '{$branchName}'";
            }

            if (!$location) {
                $missing[] = "Location '{$locationName}'";
            }

            if (!$landlord) {
                $missing[] =
                    "Estate Owner (Email: '{$landlordEmail}', Phone: '{$landlordPhone}')";
            }

            if (!empty($missing)) {

                Log::error(
                    "Apartment row {$rowIndex} has missing references.",
                    [
                        'missing' => $missing,
                        'row' => $data
                    ]
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Apartment Information
            |--------------------------------------------------------------------------
            */

            $unitNumber = trim(
                (string) $this->value(
                    $data,
                    'unit number'
                )
            );

            $address = trim(
                (string) $this->value(
                    $data,
                    'address'
                )
            );

            if ($unitNumber === '') {

                Log::warning(
                    "Apartment row {$rowIndex} has no unit number."
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Duplicate Check
            |--------------------------------------------------------------------------
            */

            $exists = ApartmentIdentity::where([

                'branch_id' => $branch->id,

                'location_models_id' => $location->id,

                'shelter_id' => $shelter->id,

                'unit_number' => $unitNumber

            ])->exists();

            if ($exists) {

                Log::warning(
                    "Duplicate apartment skipped.",
                    [
                        'row' => $rowIndex,
                        'unit_number' => $unitNumber
                    ]
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Generate Apartment Code
            |--------------------------------------------------------------------------
            */

            $generator = new ApartmentIdentity();

            $uniqueCode = $generator->generateUniqueCode(

                $branch->id,

                $location->id

            );

            /*
            |--------------------------------------------------------------------------
            | Create Apartment
            |--------------------------------------------------------------------------
            */

            $apartment = ApartmentIdentity::create([

                'branch_id' => $branch->id,

                'location_models_id' => $location->id,

                'shelter_id' => $shelter->id,

                'landlord_id' => $landlord->id,

                'address' => $address,

                'unit_number' => $unitNumber,

                'unique_code' => $uniqueCode

            ]);

            /*
            |--------------------------------------------------------------------------
            | Amenities
            |--------------------------------------------------------------------------
            */

            foreach ($amenities as $amenity) {

                $value = $this->value(
                    $data,
                    $amenity->name
                );

                $value = is_numeric($value)
                    ? (int) $value
                    : 0;

                Shelter_Amenities::updateOrCreate(

                    [

                        'id_apartment_id' => $apartment->id,

                        'amenity_id' => $amenity->id

                    ],

                    [

                        'branch_id' => $branch->id,

                        'amenity_number' => $value

                    ]

                );

                AmenitySize::updateOrCreate(

                    [

                        'apartment_id' => $apartment->id,

                        'amenity_id' => $amenity->id

                    ],

                    [

                        'amenity_name' => $amenity->name,

                        'amenity_size' => $value

                    ]

                );
            }

            Log::info(
                "Apartment imported successfully.",
                [
                    'row' => $rowIndex,
                    'unit_number' => $unitNumber,
                    'unique_code' => $uniqueCode
                ]
            );

        } catch (\Throwable $e) {

            Log::error(
                "Apartment import failed.",
                [
                    'row' => $rowIndex,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
        }
    }
}
    /*
    |--------------------------------------------------------------------------
    | Parse Date
    |--------------------------------------------------------------------------
    |
    | Supports:
    | - Excel serial dates
    | - dd/mm/yyyy
    | - mm/dd/yyyy
    | - yyyy-mm-dd
    | - Any Carbon supported format
    |
    */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {

            // Excel numeric serial date
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }

            $value = trim((string) $value);

            // Remove duplicate spaces
            $value = preg_replace('/\s+/', ' ', $value);

            return Carbon::parse($value)
                ->format('Y-m-d');

        } catch (\Throwable $e) {

            Log::warning(
                "Unable to parse date.",
                [
                    'value' => $value,
                    'message' => $e->getMessage()
                ]
            );

            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Store Private Image
    |--------------------------------------------------------------------------
    */
    private function storePrivateImage(
        string $sourceFile,
        string $directory
    ): ?string {

        $sourceFile = trim($sourceFile);

        if ($sourceFile === '') {
            return null;
        }

        if (!file_exists($sourceFile)) {

            Log::warning(
                "Image file not found.",
                [
                    'file' => $sourceFile
                ]
            );

            return null;
        }

        $extension = strtolower(
            pathinfo(
                $sourceFile,
                PATHINFO_EXTENSION
            )
        );

        $allowed = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        if (!in_array($extension, $allowed)) {

            Log::warning(
                "Unsupported image extension.",
                [
                    'extension' => $extension,
                    'file' => $sourceFile
                ]
            );

            return null;
        }

        try {

            $filename = (string) Str::uuid()
                . '.'
                . $extension;

            $path = $directory . '/' . $filename;

            Storage::disk('private')->put(

                $path,

                file_get_contents($sourceFile)

            );

            return $path;

        } catch (\Throwable $e) {

            Log::error(
                "Unable to store image.",
                [
                    'file' => $sourceFile,
                    'message' => $e->getMessage()
                ]
            );

            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Determine whether row is empty
    |--------------------------------------------------------------------------
    */
    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $cell) {

            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }
    private function normalizeIdentificationMethod(?string $method): ?string
{
    if (empty($method)) {
        return null;
    }

    $method = strtolower(trim($method));

    $method = preg_replace('/[\s\-_]+/', ' ', $method);

    return match ($method) {

        'driver licence',
        'driver license',
        'drivers licence',
        'drivers license',
        'driver_licence',
        'driver_license'
            => 'driver_licence',

        'nin',
        'national id',
        'national identification number'
            => 'nin',

        'nis',
        'immigration',
        'immigration service'
            => 'nis',

        'passport',
        'international passport'
            => 'passport',

        default => null,
    };
}
}