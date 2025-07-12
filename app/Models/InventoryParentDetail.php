<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryParentDetail extends Model
{
    protected $table = 'inventory_parent_detail';
    protected $fillable = ['product_id', 'inventory_parent_id', 'purchase_order_detail_id', 'sales_doc', 'qty'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }
}
