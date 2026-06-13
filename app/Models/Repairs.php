<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repairs extends Model
{
    use HasFactory;

    protected $table = 'repairs';

    protected $fillable = [
        'block_id',
        'apartment_id',
        'unit_number',
        'received_date',
        'progress',
        'status',
        'repair_type',
        'deadline_timeframe',
        'issue',
        'appointment_timeframe',
        'description',
        'action_timeline',
        'assigned_to',
        'ref',
        'due_date',
        'appointment',
        'completion_date',
        'location_id'
    ];

    protected $dates = [
        'received_date',
        'due_date',
        'completion_date',
        'created_at',
        'updated_at',
    ];

    // Optional: Define relationships if needed
    public function block()
    {
        return $this->belongsTo(BlockModel::class, 'block_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }
}
