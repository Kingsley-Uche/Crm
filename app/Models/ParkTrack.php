<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkTrack extends Model
{
    protected $fillable = [
        'permit_id',
        'inbound_time',
        'outbound_time',
        'inbound_admin_id',
        'outbound_admin_id'
    ];

    // Relationship to ParkPermit
    public function parkPermit()
    {
        return $this->belongsTo(ParkPermits::class, 'permit_id');
    }

    // Relationship to Inbound Admin
    public function inboundAdmin()
    {
        return $this->belongsTo(AdminModel::class, 'inbound_admin_id');
    }

    // Relationship to Outbound Admin
    public function outboundAdmin()
    {
        return $this->belongsTo(AdminModel::class, 'outbound_admin_id');
    }
}
