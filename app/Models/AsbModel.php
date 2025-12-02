<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsbModel extends Model
{
    protected $table = 'asb';

    protected $fillable = [
        'block_id',
        'apartment_id',
        'unit_number',
        'status',
        'appointment',
        'completion_date',
        'due_date',
        'assigned_to',
        'ref',
        'reporter_email',
        'crime_reference',
        'received_date',
        'video',
        'image',
        'audio',
        'document',
        'issue',
        'description',
    ];

    public static function generateRef(): string
    {
        do {
            $ref = 'CTR_ASB' . random_int(100000, 999999);
        } while (self::where('ref', $ref)->exists());

        return $ref;
    }

    public function block()
    {
        return $this->belongsTo(BlockModel::class, 'block_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }
}
