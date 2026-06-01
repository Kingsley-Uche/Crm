<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AmenitySize extends Model
{
    use HasFactory;

    protected $table = 'amenity_sizes';

    protected $fillable = [
        'amenity_size',
        'amenity_id',
        'apartment_id',
        'amenity_name',
        'branch_id',
        'location_models_id',
        'shelter_id',
    ];

    /**
     * Get the amenity associated with this size.
     */
    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }

    /**
     * Get the apartment associated with this amenity size.
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
