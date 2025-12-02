<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionsModel extends Model
{
    use SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}