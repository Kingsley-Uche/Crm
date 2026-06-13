<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PestModel extends Model
{
    protected $table = 'pest_models';

    protected $fillable = [
        'block_id',
        'apartment_id',
        'issue_type',
        'description',
        'status',
        'ref',
        'image',
        'received_date',
        'progress',
        'deadline_timeframe',
        'appointment_timeframe',
        'action_timeline',
        'assigned_to',
        'due_date',
        'appointment',
        'completion_date',
        'pest_control_fee',
        'location_id'
    ];

   public function block()
    {
        return $this->belongsTo(BlockModel::class, 'block_id');  // 'block_models_id' is the foreign key in block_shelters table
    }


    // Optional: Add apartment relation if it exists
    public function apartment()
    {
        return $this->belongsTo(ApartmentIdentity::class, 'apartment_id'); // Updated to ApartmentIdentity
    }

public static function generateRef(): string
{
    do {
        $ref = 'PST-' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    } while (self::where('ref', $ref)->exists());

    return $ref;
}


}
