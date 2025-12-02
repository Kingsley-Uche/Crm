<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenancyTypeModel extends Model
{

    protected $table = 'tenancy_types'; // Ensure it matches the database table name

    protected $fillable = ['name']; // Allow mass assignment for 'name' field

}
