<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = [
        'purchase_order_id',
        'purc_doc',
        'sales_doc',
        'qty_item',
        'storage_id'
    ];

    public function storage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }
}
