<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundDetail extends Model
{
    protected $table = 'outbound_detail';
    protected $fillable = [
        'outbound_id',
        'inventory_package_item_id',
        'qty'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function inventoryPackageItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InventoryPackageItem::class, 'inventory_package_item_id');
    }

    public function outboundDetailSn(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OutboundDetailSn::class, 'outbound_detail_id');
    }
}
