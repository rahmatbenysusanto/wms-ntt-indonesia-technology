<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferLocation extends Model
{
    protected $table = 'transfer_location';
    protected $fillable = [
        'inventory_package_id',
        'old_storage',
        'new_storage',
        'created_by'
    ];

    public function inventoryPackage(): BelongsTo
    {
        return $this->belongsTo(InventoryPackage::class, 'inventory_package_id', 'id');
    }

    public function oldLocation(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'old_storage', 'id');
    }

    public function newLocation(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'new_storage', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
