<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionTrackerModel extends Model
{
    protected $table='trackers';
    protected $fillable = [
        'plan_id',
        'plan_name',
        'start_time',
        'end_time',
        'fee',
        'status',
        'plan_features'
    ];    
    //
}
