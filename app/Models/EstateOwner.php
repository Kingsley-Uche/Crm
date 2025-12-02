<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstateOwner extends Model
{
    // Use soft deletes if you want to keep deleted records
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'estate_owners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fName',
        'lName',
        'email',
        'phones',
        'means_of_identification',
        'identification_image',
        'address',
        'next_of_kin',
        'next_of_kin_phone',
        'bank_name',
        'account_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the full name of the estate owner.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->fName} {$this->lName}";
    }

    /**
     * Scope a query to search estate owners by name or email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where('fName', 'like', "%{$search}%")
                      ->orWhere('lName', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Get the identification image URL.
     *
     * @return string|null
     */
    public function getIdentificationImageUrlAttribute()
    {
        return $this->identification_image 
            ? \Storage::disk('public')->url($this->identification_image)
            : null;
    }
}