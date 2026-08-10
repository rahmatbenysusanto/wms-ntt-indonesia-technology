<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendingOutbound extends Model
{
    protected $table = 'pending_outbounds';
    protected $fillable = [
        'customer_id',
        'purc_doc',
        'sales_docs',
        'qty_item',
        'qty',
        'delivery_date',
        'delivery_note_number',
        'ntt_dn',
        'deliv_loc',
        'deliv_dest',
        'koli',
        'note',
        'status',
        'created_by',
        'converted_by',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'sales_docs' => 'array',
            'delivery_date' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(PendingOutboundDetail::class, 'pending_outbound_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by', 'id');
    }
}
