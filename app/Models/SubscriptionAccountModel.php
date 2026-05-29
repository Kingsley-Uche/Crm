<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionAccountModel extends Model
{
    //
     protected $table='subscription_account';
    protected $fillable = [
        'plan_id',
        'start_time',
        'end_time',
        'tracker_id',
        'fee',
        'status'
    ];


    public function plan()
{
    return $this->belongsTo(
        SubscriptionPlanModel::class,
        'plan_id'
    )->select([
        'id',
        'name',
        'price_per_month',
        'number_admins',
        'number_branches',
        'number_apartments',
        'number_property_managers',
        'discount_percentage'
    ]);
}
}
