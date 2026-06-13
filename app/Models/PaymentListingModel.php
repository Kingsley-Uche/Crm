<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentListingModel extends Model
{
    use HasFactory;

    protected $table = 'payment_listing_models';

    protected $fillable = [
        'name',
        'qty',
        'unit_charge',
        'amount',
        'invoice_id',
        'tenant_id',
        'apartment_id',
        'location_id'
    ];

    /**
     * Relationship: Payment belongs to an Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(InvoiceModel::class, 'invoice_id');
    }

    /**
     * Relationship: Payment belongs to a Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(TenantModel::class, 'tenant_id');
    }
}