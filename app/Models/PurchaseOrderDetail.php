<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'status',
        'qty_qc',
        'sales_doc',
        'item',
        'material',
        'po_item_desc',
        'prod_hierarchy_desc',
        'acc_ass_cat',
        'vendor_name',
        'customer_name',
        'stor_loc',
        'sloc_desc',
        'valuation',
        'po_item_qty',
        'net_order_price',
        'currency',
        'price_idr',
        'price_date'
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
