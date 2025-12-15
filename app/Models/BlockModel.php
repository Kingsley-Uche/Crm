<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockModel extends Model
{
    protected $fillable = [
        'name',
        'address',
        'landlord_id',
        'location_id',
    ];

    /**
     * Get the landlord that owns the block.
     */
    public function landlord()
    {
        return $this->belongsTo(EstateOwner::class, 'landlord_id');
    }

    /**
     * Get the state that the block belongs to.
     */
   

    /**
     * Get the local government that the block belongs to.
     */
   
    public function location()
{
    return $this->belongsTo(LocationModel::class, 'location_id', 'id');
}


    /**
     * Get the shelters associated with the block.
     */
    public function shelters()
    {
        // Assuming block_shelters.block_models_id is the foreign key for blocks
        return $this->hasMany(Block_Shelter::class, 'block_models_id', 'id');
    }
    
    public function shelt()
{
    return $this->hasMany(Shelter::class, 'id');
}

    
     public function apartments()
    {
        // Assuming block_shelters.block_models_id is the foreign key for blocks
        return $this->hasMany(ApartmentIdentity::class, 'block_models_id', 'id');
    }
    
     protected static function booted()
    {
        static::deleting(function ($block) {
            // Delete related block shelters when a block is deleted
            $block->shelters()->delete();
        });
    }
}
