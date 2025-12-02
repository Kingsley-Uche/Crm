<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FobModel extends Model
{
    protected $table = 'fobs'; // Optional: specify table name if different

    protected $fillable = [
        'tenant_id',
        'fob_uid',       // Unique hardware ID of the fob
        'make',          // Manufacturer (e.g., HID, Paxton)
        'model',         // Model name or code
        'type',          // Technology type: rfid, nfc, ble, etc.
        'fob_status',    // active, lost, malfunctioning, deactivated
        'request_reason',// Reason for request (lost, broken, new)
        'request_date',  // When the replacement or issue was requested
        'issued_date',   // When the new fob was issued
        'fee',           // Any replacement or extra charge
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'issued_date' => 'datetime',
        'fee' => 'decimal:2',
    ];

    // Optional relationships
    public function tenant()
    {
        return $this->belongsTo(TenantModel::class);
    }

public function getRouteKeyName()
{
    return 'fob_id'; // if you want to use a custom key
}

}
