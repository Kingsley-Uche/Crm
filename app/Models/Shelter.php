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
    
}
