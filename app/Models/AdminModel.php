<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Extend the base Auth class for authentication
use Illuminate\Notifications\Notifiable; // Add this if you want notifications functionality

class AdminModel extends Authenticatable // Use Authenticatable instead of Model
{
    use Notifiable; // Optional if you want to use notifications

    // The table associated with the model
    protected $table = 'admin_models'; // Ensure this matches your table name

    // Fields that can be mass assigned
    protected $fillable = [
        'fName', 'lName', 'email', 'phone', 'password', 'is_active', 'is_system_admin', 'created_by_admin_id','role_id',
    ];

    // Fields that should be hidden when serialized (e.g., to JSON)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Fields that should be cast to a specific data type
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel will automatically hash when assigning to the 'password'
    ];
}
