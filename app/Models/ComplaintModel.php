<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplaintModel extends Model
{
    use HasFactory;

    protected $table = 'complaint_models';

    protected $fillable = [
        'block_id',
        'apartment_id',
        'unit_number',
        'subject',
        'description',
        'status',
        'assigned_to',
        'tenant_id',
        'phone',
        'email',
        'created_by_admin_id',
        'received_date',
        'resolved_date',
        'action_taken', // Add this if it's in your database
    ];

    // Relationships
    public function block()
    {
        return $this->belongsTo(BlockModel::class, 'block_id');
    }

    public function apartment()
    {
        return $this->belongsTo(ApartmentIdentity::class, 'apartment_id');
    }

    public function tenant()
    {
        return $this->belongsTo(TenantModel::class, 'tenant_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
    public function location(){
        return $this->belongsTo(LocationModel::class, 'location_id');

    }
}
