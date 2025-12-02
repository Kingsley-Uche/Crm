<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalGvt extends Model
{
    protected $table = 'local_governments';
    protected $fillable = ['id', 'state_id', 'name'];

    public function state()
    {
        return $this->belongsTo(States::class, 'state_id', 'id');
    }
}