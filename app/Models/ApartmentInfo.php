<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentInfo extends Model
{
    use HasFactory;
    
    protected $table = 'apartment_infos';
    
    protected $fillable = [
        'tenancy_type',
        'pro_sco_code',
        'property_ref',
        'ownership',
        'admin_unit',
        'unit_number',
        'apartment_id',
        'block_models_id', 
        'shelter_id',
        'post_code',
        'address'
    ];

    /**
     * Get the apartment that owns the info.
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(ApartmentIdentity::class, 'apartment_id');
    }
}
