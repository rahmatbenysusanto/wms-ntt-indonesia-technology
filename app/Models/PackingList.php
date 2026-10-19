<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackingList extends Model
{
    protected $table = 'packing_lists';

    protected $fillable = [
        'doc_no',
        'customer_id',
        'sales_docs',
        'qty_item',
        'qty',
        'delivery_date',
        'deliv_loc',
        'deliv_dest',
        'dn_no',
        'cust_po_no',
        'pic',
        'ship_via',
        'note',
        'status',
        'created_by',
        'updated_by',
        'released_by',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'sales_docs' => 'array',
            'delivery_date' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(PackingListDetail::class, 'packing_list_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by', 'id');
    }
}