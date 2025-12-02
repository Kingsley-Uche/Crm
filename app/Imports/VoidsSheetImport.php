<?php
namespace App\Imports;

use App\Models\VoidsModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class VoidsSheetImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            VoidsModel::updateOrCreate(
                ['void_ref' => $row['void_ref']],
                [
                    'void_path' => $row['void_path'] ?? null,
                    'void_classification' => $row['void_classification'] ?? null,
                    'hfi_code' => $row['hfi_code'] ?? null,
                    'uprn' => $row['uprn'] ?? null,
                    'property_ref' => $row['property_ref'] ?? null,
                    'ten_reason' => $row['ten_reason'] ?? null,
                    'address' => $row['address'] ?? null,
                    'property_type' => $row['property_type'] ?? null,
                    'property_subtype' => $row['property_subtype'] ?? null,
                    'bedrooms' => isset($row['bedrooms']) ? (int) $row['bedrooms'] : null,
                    'void_status' => $row['void_status'] ?? null,
                    'vin_sco_code' => $row['vin_sco_code'] ?? null,
                    'days_void' => isset($row['days_void']) ? (int) $row['days_void'] : null,
                    'termination_date' => $this->parseExcelDate($row['termination_date'] ?? null),
                    'ready_for_let_date' => $this->parseExcelDate($row['ready_for_let_date'] ?? null),
                    'management_unit' => $row['management_unit'] ?? null,
                    'updates' => $row['updates'] ?? null,
                    'previous_call_over' => $row['previous_call_over'] ?? null,
                ]
            );
        }
    }

    private function parseExcelDate($date)
    {
        if (empty($date)) return null;

        try {
            // Check if the date is numeric (Excel date format)
            if (is_numeric($date)) {
                return Carbon::instance(Date::excelToDateTimeObject($date));
            }

            // Attempt to parse it as a string (e.g., "DD/MM/YYYY")
            return Carbon::createFromFormat('d/m/Y', $date) ?: Carbon::parse($date);
        } catch (\Exception $e) {
            Log::warning("Date parse failed: $date. Error: " . $e->getMessage());
            return null;
        }
    }
}
