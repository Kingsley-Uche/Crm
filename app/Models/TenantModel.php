<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantModel extends Model
{
    use HasFactory;

    // Specify the table associated with the model (adjust table name as needed)
    protected $table = 'tenants';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        // Step 1: Occupant Basic Details
        'full_name',
        'date_of_birth',
        'gender',

        // Step 2: Identification Details
        'nationality',
        'state',
        'address',
        'id_method',
        'identification_image',
        'passport_photograph',

        // Step 3: Contact Details
        'mobile_number',
        'home_number',
        'occupant_email',
        'emergency_contact',
        'emergency_email',

        // Step 4: Guarantor Details
        'guarantor_full_name',
        'guarantor_address',
        'guarantor_phone',
        'guarantor_email',
        'guarantor_passport',
    ];

    // Define casts for specific attributes
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // Example relationship (if a Tenant can have many leases)
    // public function leases()
    // {
    //     return $this->hasMany(Lease::class);
    // }
}
