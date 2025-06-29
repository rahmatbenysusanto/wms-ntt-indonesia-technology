<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryDetail extends Model
{
    protected $table = 'inventory_detail';
    protected $fillable = [
        'inventory_id',
        'purchase_order_detail_id',
        'quality_control_detail_id',
        'purc_doc',
        'sales_doc',
        'type',
        'qty',
        'parent_id'
    ];

    public function purchaseOrderDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PurchaseOrderDetail::class, 'id', 'purchase_order_detail_id');
    }
}
