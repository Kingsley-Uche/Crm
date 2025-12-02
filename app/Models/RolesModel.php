<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolesModel extends Model
{
    use SoftDeletes;

    protected $table = 'roles_models';

    protected $fillable = ['name', 'description'];

    protected $dates = ['deleted_at'];

    // This connects the role to permissions through the pivot table
    public function permissions()
    {
        return $this->belongsToMany(PermissionsModel::class, 'role_permission', 'role_id', 'permission_id')
                    ->withTimestamps(); // optional
    }
}
