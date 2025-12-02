<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParkPermits extends Model
{
    use HasFactory;

    protected $table = 'park_permits';

    protected $fillable = [
        'fname',
        'lname',
        'phone',
        'email',
        'permit_name',
        'code',
        'park_category_id',
        'park_model_id',
        'start_time',
        'end_time',
        'fee',
        'uniqueId',
        'read',
        'pass_code'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'fee' => 'decimal:2',
    ];

    public function parkCategory(): BelongsTo
    {
        return $this->belongsTo(ParkCategory::class, 'park_category_id');
    }

    public function park(): BelongsTo
    {
        return $this->belongsTo(ParkModel::class, 'park_model_id');
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(ParkTaxes::class, 'permit_taxes', 'permit_id', 'tax_id');
    }

    /**
     * Generate a unique 12-digit code with a duration-based suffix (D, W, or M).
     *
     * @param array $data Containing first_name, last_name, start_date, and end_date
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function GenCode(array $data): string
    {
        if (!isset($data['first_name'], $data['last_name'], $data['start_date'], $data['end_date'])) {
            throw new \InvalidArgumentException('First name, last name, start date, and end date are required to generate the permit code.');
        }

        $maxAttempts = 100;
        $attempt = 0;

        // Calculate duration suffix using Carbon
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $daysDiff = $startDate->diffInDays($endDate);

        // Determine suffix: D (< 7 days), W (7 to < 30 days), M (≥ 30 days)
        $suffix = $daysDiff < 7 ? 'D' : ($daysDiff < 30 ? 'W' : 'M');

        do {
            if ($attempt++ >= $maxAttempts) {
                throw new \RuntimeException('Unable to generate a unique permit code after multiple attempts.');
            }

            // Generate a 6-digit timestamp component (YYMMDD)
            $timeComponent = now()->format('ymd'); // e.g., 250526

            // Generate a 4-digit hash from first_name and last_name
            $nameHash = substr(abs(crc32($data['first_name'] . $data['last_name'])), 0, 4);
            $nameHash = str_pad($nameHash, 4, '0', STR_PAD_LEFT); // e.g., 1234

            // Generate a 2-digit random component
            $randomComponent = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT); // e.g., 42

            // Combine: 12 digits + suffix
            $code = $timeComponent . $nameHash . $randomComponent . $suffix;
        } while (self::where('uniqueId', $code)->exists());

        return env('ABR').'P-'.$code;
    }
    public static function generatePasscode(): string
    {
        $maxAttempts = 100;
        $attempt = 0;

        do {
            if ($attempt++ >= $maxAttempts) {
                throw new \RuntimeException('Unable to generate a unique passcode after multiple attempts.');
            }

            $code = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);

        } while (self::where('pass_code', $code)->exists()); 

        return $code;
    }

}