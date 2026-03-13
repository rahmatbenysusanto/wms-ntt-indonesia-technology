<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnChangeLog extends Model
{
    protected $table = 'sn_change_log';
    protected $fillable = [
        'inventory_package_item_sn_id',
        'inventory_package_id',
        'inventory_package_item_id',
        'old_serial_number',
        'new_serial_number',
        'notes',
        'changed_by',
    ];

    public function inventoryPackage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InventoryPackage::class, 'inventory_package_id');
    }

    public function inventoryPackageItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InventoryPackageItem::class, 'inventory_package_item_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }
}
