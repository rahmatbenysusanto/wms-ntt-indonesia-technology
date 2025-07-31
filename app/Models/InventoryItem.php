<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_item';
    protected $fillable = [
        'purc_doc',
        'sales_doc',
        'product_id',
        'stock',
        'storage_id',
        'type'
    ];

    public function storage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purc_doc', 'purc_doc');
    }
}
