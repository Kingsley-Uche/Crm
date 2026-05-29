<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanModel extends Model
{
    //
    protected $table='plans';
    protected $fillable = [
        'name',
        'description',
        'number_admins',
        'number_branches',
        'number_apartments',
        'number_property_managers',
        'price_per_month',
        'discount_min_months',
        'discount_percentage',
        'is_active'
    ];
}
