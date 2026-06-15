<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\SingleSheetImport;
use Illuminate\Support\Facades\Log;

class MultiTableImport implements WithMultipleSheets, SkipsUnknownSheets
{
    private array $runSheets = [];

    /**
     * Map your exact spreadsheet tab names to their operational routing tags.
     */
    public function sheets(): array
    {
        $sheetMapping = [
          
            'Branch'        => new SingleSheetImport('branch'),
            'Location'      => new SingleSheetImport('location'),
            'Tenants'       => new SingleSheetImport('tenants'),
            'Apartments'    => new SingleSheetImport('apartments'),
            'Shelter Types' => new SingleSheetImport('shelter types'),
        ];
        

        // Track what keys are configured to run
        $this->runSheets = array_keys($sheetMapping);
        
        return $sheetMapping;
    }

    /**
     * Handles gracefully logging tabs that exist in Excel but aren't explicitly mapped.
     */
    public function onUnknownSheet($sheetName)
    {
        Log::info("Skipped unmapped Excel sheet: {$sheetName}");
    }

    /**
     * Helper to return mapped configurations back to your controller.
     */
    public function getImportedSheets(): array
    {
        return $this->runSheets;
    }
}