<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackingListRepack extends Model
{
    protected $table = 'packing_list_repacks';

    protected $fillable = [
        'packing_list_id',
        'koli_no',
        'qty_box',
        'length',
        'width',
        'height',
        'weight',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PackingListDetail::class, 'packing_list_repack_id', 'id');
    }
}
