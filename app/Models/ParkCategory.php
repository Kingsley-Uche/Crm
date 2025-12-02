<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'features',
    ];

    /**
     * Get the parking permits associated with this category.
     *
     * @return HasMany
     */
    public function permits(): HasMany
    {
        return $this->hasMany(ParkPermit::class, 'park_category_id');
    }
}