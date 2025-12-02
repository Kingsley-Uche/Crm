<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTime extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_times';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_frequency',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'payment_frequency' => 'string',
    ];
    
  
}
