<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    //this is the model for the manager of the property, this will be used to assign a manager to a property and also to assign a manager to a branch
    protected $fillable = [
        'name', 'email', 'phone','password'
    ];
    public function apartments()
    {
        return $this->hasMany(ApartmentIdentity::class, 'property_manager_id');
    }
}
