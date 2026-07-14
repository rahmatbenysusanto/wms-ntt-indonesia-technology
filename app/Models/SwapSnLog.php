<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SwapSnLog extends Model
{
    protected $table = 'swap_sn_log';

    protected $fillable = [
        'inventory_package_item_sn_id',
        'inventory_package_id',
        'inventory_package_item_id',
        'old_serial_number',
        'new_serial_number',
        'reference_serial_number',
        'reason',
        'changed_by',
    ];

    public function inventoryPackage(): BelongsTo
    {
        return $this->belongsTo(InventoryPackage::class, 'inventory_package_id');
    }

    public function inventoryPackageItem(): BelongsTo
    {
        return $this->belongsTo(InventoryPackageItem::class, 'inventory_package_item_id');
    }

    public function inventoryPackageItemSn(): BelongsTo
    {
        return $this->belongsTo(InventoryPackageItemSN::class, 'inventory_package_item_sn_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
