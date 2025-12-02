<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'address',
        'category_id'
    ];

    /**
     * Get the parking permits associated with this park.
     *
     * @return HasMany
     */
    public function permits(): HasMany
    {
        return $this->hasMany(ParkPermit::class, 'park_models_id');
    }
    public function category(){
        return $this->belongsTo(ParkCategory::class,'category_id');
    }
}
