<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class permitTaxes extends Model
{
 
    protected $table = 'permit_taxes';

    protected $fillable = ['permit_id', 'tax_id'];

    /**
     * Relationships
     */
    public function permit()
    {
        return $this->belongsTo(ParkPermit::class);
    }

    public function tax()
    {
        return $this->belongsTo(ParkTaxes::class);
    }
}



