<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelter extends Model
{
    //
    protected $table ='shelters';
    protected $fillable = ['name','created_by', 'is_active'];
    
     public function blockShelters()
    {
        return $this->hasMany(Block_Shelter::class, 'shelter_id');
    }
    public function apartments()
    {
        return $this->hasMany(ApartmentIdentity::class, 'shelter_id')->select('id', 'branch_id', 'location_models_id','shelter_id');
    }
    
}
