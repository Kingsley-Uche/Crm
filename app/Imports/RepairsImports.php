<?php

namespace App\Imports;

use App\Models\BlockModel;
use App\Models\Maintenance;
use App\Models\Repairs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class RepairsImports implements ToArray
{
    /**
     * Process the Excel data as an array.
     *
     * @param array $rows
     * @return void
     */
    public function array(array $rows)
    {
        if (empty($rows)) {
            Log::warning('Empty Excel file provided');
            return;
        }

        // Extract header and validate required columns
        $header = array_shift($rows);
        $requiredColumns = ['Block', 'Door'];
        if (!$this->validateHeader($header, $requiredColumns)) {
            Log::error('Invalid or missing required columns in Excel file');
            return;
        }

        // Step 1: Extract unique block names
        $blockNames = collect($rows)
            ->map(fn($row) => strtolower(trim($row[array_search('Block', $header)] ?? '')))
            ->filter()
            ->unique()
            ->values();

        // Step 2: Handle missing blocks
        $this->createMissingBlocks($blockNames);

        // Step 3: Fetch all blocks with apartments
        $blocks = BlockModel::with('apartments')
            ->whereIn('name', $blockNames)
            ->get();

        // Step 4: Build apartment map for quick lookup
        $apartmentMap = $this->buildApartmentMap($blocks);

        // Step 5: Process rows and prepare maintenance data
        $maintenanceData = $this->processRows($rows, $header, $blocks, $apartmentMap);
        // Step 6: Bulk insert maintenance records
        if (!empty($maintenanceData)) {
            Repairs::insert($maintenanceData);
            Log::info('Successfully imported ' . count($maintenanceData) . ' maintenance records');
        } else {
            Log::warning('No valid maintenance records to import');
        }
        return true;
    }

    /**
     * Validate the Excel header for required columns.
     *
     * @param array $header
     * @param array $requiredColumns
     * @return bool
     */
    private function validateHeader(array $header, array $requiredColumns): bool
    {
        return collect($requiredColumns)->every(fn($col) => in_array($col, $header));
    }

    /**
     * Create missing blocks in the database.
     *
     * @param \Illuminate\Support\Collection $blockNames
     * @return void
     */
    private function createMissingBlocks($blockNames): void
    {
        $existingBlocks = BlockModel::whereIn('name', $blockNames)->pluck('name')->map(fn($name) => strtolower($name));
        $newBlocks = $blockNames->diff($existingBlocks)->map(function ($name) {
            return [
                'name' => $name,
                'slug' => \Str::slug($name),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if (!empty($newBlocks)) {
            BlockModel::insert($newBlocks);
            Log::info('Created ' . count($newBlocks) . ' new blocks');
        }
    }

    /**
     * Build a map of apartments for quick lookup.
     *
     * @param \Illuminate\Database\Eloquent\Collection $blocks
     * @return array
     */
    private function buildApartmentMap($blocks): array
    {
        $apartmentMap = [];
        foreach ($blocks as $block) {
            foreach ($block->apartments as $apartment) {
                $key = strtolower($block->name) . '_' . (int) $apartment->unit_number;
                $apartmentMap[$key] = [
                    'block_id' => $block->id,
                    'apartment_id' => $apartment->id,
                    'unit_number' => $apartment->unit_number,
                ];
            }
        }
        return $apartmentMap;
    }

    /**
     * Process Excel rows and prepare maintenance data.
     *
     * @param array $rows
     * @param array $header
     * @param \Illuminate\Database\Eloquent\Collection $blocks
     * @param array $apartmentMap
     * @return array
     */
    private function processRows(array $rows, array $header, $blocks, array $apartmentMap): array
    {
        $blockNameToId = $blocks->mapWithKeys(fn($block) => [strtolower($block->name) => $block->id]);

        return collect($rows)
            ->flatMap(function ($row) use ($header, $blockNameToId, $apartmentMap) {
                $assoc = array_combine($header, $row);
                $blockName = strtolower(trim($assoc['Block'] ?? ''));
                $doors = $this->getAddress($assoc['Door'] ?? '');

                if (!$blockName || empty($doors) || !isset($blockNameToId[$blockName])) {
                    return [];
                }

                $blockId = $blockNameToId[$blockName];
                $receivedDate = $this->convertDate($assoc['Received'] ?? null);
                $dueDate = $this->convertDate($assoc['Due Date'] ?? null);
                $completionDate = $this->convertDate($assoc['Completion Date'] ?? null);
                $appointment = $this->convertDate($assoc['Appointment'] ?? null);

                return collect($doors)->map(function ($door) use ($blockId, $assoc, $apartmentMap, $blockName, $receivedDate, $dueDate, $completionDate, $appointment) {
                    $key = $blockName . '_' . (int) $door;
                    $apartment = $apartmentMap[$key] ?? null;

                    if (!$apartment) {
                        Log::warning("Apartment not found for block: $blockName, door: $door");
                        return null;
                    }

                    return [
                        'block_id' => $blockId,
                        'apartment_id' => $apartment['apartment_id'],
                        'unit_number' => $apartment['unit_number'],
                        'received_date' => $receivedDate,
                        'progress' => trim($assoc['Progress'] ?? ''),
                        'status' => trim($assoc['Status of job'] ?? ''),
                        'repair_type' => trim($assoc['Repair Report Type'] ?? ''),
                        'deadline_timeframe' => trim($assoc['Deadline Time frame'] ?? ''),
                        'issue' => trim($assoc['Repair Issue'] ?? ''),
                        'appointment_timeframe' => trim($assoc['Appointment Timeframe'] ?? ''),
                        'description' => trim($assoc['Description of Repair'] ?? ''),
                        'action_timeline' => trim($assoc['Action Timeline'] ?? ''),
                        'assigned_to' => trim($assoc['Assigned To'] ?? ''),
                        'ref' => trim($assoc['REF'] ?? ''),
                        'due_date' => $dueDate,
                        'appointment' => $appointment,
                        'completion_date' => $completionDate,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->values();
            })->filter()->toArray();
            
            
            
    }

    /**
     * Parse and return door numbers from a string.
     *
     * @param string $door
     * @return array
     */
    private function getAddress(string $door): array
    {
        $door = trim($door);
        $addresses = [];

        if (is_numeric($door)) {
            $addresses[] = (int) $door;
        } elseif (preg_match('/^(\d+)\s*-\s*(\d+)$/', $door, $matches)) {
            $start = (int) $matches[1];
            $end = (int) $matches[2];
            $step = ($start % 2 === $end % 2) ? 2 : 1;

            if ($start > $end) {
                [$start, $end] = [$end, $start];
            }

            for ($i = $start; $i <= $end; $i += $step) {
                $addresses[] = $i;
            }
        } elseif (preg_match('/^odd\s+(\d+)\s*-\eningen\w+([0-9]{1,2})?\s*([A-Za-z]+)?\s*([0-9]+)?$/i', $door, $matches)) {
            $start = (int) $matches[1];
            $end = (int) $matches[2];

            for ($i = $start; $i <= $end; $i += 2) {
                $addresses[] = $i;
            }
        } elseif (preg_match('/^even\s+(\d+)\s*-\s*(\d+)$/i', $door, $matches)) {
            $start = (int) $matches[1];
            $end = (int) $matches[2];

            for ($i = $start; $i <= $end; $i += 2) {
                $addresses[] = $i;
            }
        }

        return $addresses;
    }

    /**
     * Convert Excel date (serial number or string) to Y-m-d format.
     *
     * @param mixed $date
     * @return string|null
     */
    private function convertDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::instance(Date::excelToDateTimeObject($date))->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Failed to convert date: $date. Error: " . $e->getMessage());
            return null;
        }
    }
}