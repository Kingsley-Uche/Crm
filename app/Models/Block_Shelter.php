<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block_Shelter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'block_shelters'; // Ensure this matches your database table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'block_models_id', // Correct foreign key name for the block model
        'shelter_id',
        'shelter_qty',
        'estate_owner_id',
    ];

    /**
     * Get the block that this shelter entry belongs to.
     */
    public function block()
    {
        return $this->belongsTo(BlockModel::class, 'block_models_id');  // 'block_models_id' is the foreign key in block_shelters table
    }

    /**
     * Get the shelter associated with this entry.
     */
    public function shelter()
    {
        return $this->belongsTo(Shelter::class, 'shelter_id');  // 'shelter_id' is the correct foreign key for Shelter model
    }

    /**
     * Get the estate owner (landlord) associated with this entry.
     */
    public function estateOwner()
    {
        return $this->belongsTo(EstateOwner::class, 'estate_owner_id');  // 'estate_owner_id' should match the foreign key for the EstateOwner model
    }

    /**
     * Get the amenities associated with this block shelter.
     */
    public function shelterAmenities()
    {
        return $this->hasMany(Shelter_Amenities::class, 'block_shelter_id');  // 'block_shelter_id' is the foreign key in the shelter_amenities table
    }
   
}
