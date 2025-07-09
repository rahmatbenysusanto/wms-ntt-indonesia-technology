<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductChildDetail extends Model
{
    protected $table = 'product_child_detail';
    protected $fillable = ['product_child_id', 'product_id', 'purchase_order_detail_id', 'sales_doc', 'qty'];

    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }
}
