<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingModel extends Model
{
    // Define the table name if it's different from the default (optional)
    protected $table = 'booking_models';

    // Define the fillable fields
    protected $fillable = [
        'shelter_id', 
        'payment_time_id', 
        'start_date', 
        'end_date', 
        'apartment_id',  
        'booked_by_user_id', 
        'booked_by_user_type', // Assuming this is a string to indicate user type (e.g., admin, tenant)
        'tenant_id', 
        'is_cancelled', 
        'updated_by_user_id', 
        'fee'
    ];

    // Define relationships for eager loading

    public function shelter()
    {
        return $this->belongsTo(Shelter::class);
    }

    public function blockModel()
    {
        return $this->belongsTo(BlockModel::class);
    }

    public function paymentTime()
    {
        return $this->belongsTo(PaymentTime::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function blockShelter()
    {
        return $this->belongsTo(Block_Shelter::class);
    }

    public function bookedByUser()
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function tenant()
    {
        return $this->belongsTo(TenantModel::class);
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    // Example of eager loading: $bookings = BookingModel::with('shelter', 'blockModel', 'tenant')->get();
}
