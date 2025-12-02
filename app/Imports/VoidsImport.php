<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\VoidsSheetImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use InvalidArgumentException;

class VoidsImport implements WithMultipleSheets
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function sheets(): array
    {
        $sheetNames = $this->getSheetNames();
        $imports = [];

        foreach ($sheetNames as $name) {
            $imports[$name] = new VoidsSheetImport($name);
        }

        return $imports;
    }

    /**
     * Get the names of all sheets in the Excel file.
     */
    private function getSheetNames(): array
    {
        if (!file_exists($this->filePath)) {
            throw new InvalidArgumentException('Excel file not found at: ' . $this->filePath);
        }

        try {
            $spreadsheet = IOFactory::load($this->filePath);
            return $spreadsheet->getSheetNames();
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Failed to read Excel file: ' . $e->getMessage());
        }
    }
}
