<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_order';
    protected $fillable = [
        'purc_doc',
        'customer_id',
        'vendor_id',
        'sales_doc_qty',
        'material_qty',
        'item_qty',
        'status',
        'created_by',
    ];

    public function purchaseOrderDetail(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
