<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenities extends Model
{
    use HasFactory;

    protected $table = 'amenities';

    protected $fillable = ['name', 'created_by', 'created_by_user_type',
    'is_active'];

    /**
     * Get the amenity sizes associated with this amenity.
     */
    public function amenitySizes()
    {
        return $this->hasMany(AmenitySize::class, 'amenity_id', 'id');
    }
}
