<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class VoidsModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'voids';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'void_path',
        'void_classification',
        'hfi_code',
        'uprn',
        'property_ref',
        'ten_reason',
        'void_ref',
        'address',
        'property_type',
        'property_subtype',
        'bedrooms',
        'void_status',
        'vin_sco_code',
        'days_void',
        'termination_date',
        'ready_for_let_date',
        'management_unit',
        'previous_call_over',
        'updates',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'bedrooms' => 'integer',
        'days_void' => 'integer',
        'termination_date' => 'datetime',
        'ready_for_let_date' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
    public static function generateVoidRef()
{
    do {
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    } while (self::where('void_ref', $code)->exists());

    return $code;
}

}