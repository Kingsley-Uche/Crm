<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_models';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_ref',
        'tenant_id',
        'apartment_id',
        'location_id',
        'branch_id',
        'amount',
        'balance',
        'description',
        'status',
        'due_date',
        'paid_at',
        'paid_amount',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'     => 'decimal:2',
        'balance'    => 'decimal:2',
        'due_date'   => 'date',
        'paid_amount'=>'decimal:2',
        'paid_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant()
    {
        return $this->belongsTo(TenantModel::class, 'tenant_id');
    }

    public function apartment()
    {
        return $this->belongsTo(ApartmentIdentity::class, 'apartment_id');
    }

    public function location()
    {
        return $this->belongsTo(LocationModel::class, 'location_id');
    }

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    public function paymentListings()
    {
        return $this->hasMany(PaymentListingModel::class, 'invoice_id');
    }

    /**
     * Scope for paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for pending invoices.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if invoice is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== 'paid';
    }
}