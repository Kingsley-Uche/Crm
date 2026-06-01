<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelter_Amenities extends Model
{
    // Define fillable properties for mass assignment
    protected $table='shelter_amenities';
    protected $fillable = [
        'block_models_id',
        'block_shelter_id',
        'amenity_id',
        'amenity_number',
        'id_apartment_id',
        'branch_id',
        'location_models_id'
    ];

    // Relationship with Block_Shelter model (if necessary)
    public function blockShelter()
    {
        return $this->belongsTo(Block_Shelter::class, 'block_shelter_id');
    }

public function amenities()
{
    return $this->belongsTo(Amenities::class, 'amenity_id')->select('id', 'name');
}


public function amenitySizes()
{
    return $this->hasMany(AmenitySize::class, 'apartment_id', 'id_apartment_id');
}


    private function apartment(){
          return $this->belongsTo(ApartmentIdentity::class, 'id');
    
        
    }
        
    
}
