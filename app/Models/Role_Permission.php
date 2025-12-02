<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role_Permission extends Model
{
    // Define the actual table name
    protected $table = 'role_permission';

    // Disable auto-incrementing ID (composite key is used)
    public $incrementing = false;

    // Define the primary key as a composite key type
    protected $primaryKey = ['role_id', 'permission_id'];

    // If using Laravel < 9, set this
    protected $keyType = 'int';

    // Allow mass assignment (optional)
    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    /**
     * Get the role for this permission assignment.
     */
    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }

    /**
     * Get the permission for this role assignment.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
