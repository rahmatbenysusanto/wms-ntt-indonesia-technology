<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpareRoom extends Model
{
    protected $table = 'spare_room';
    protected $fillable = [
        'outbound_id',
        'outbound_date',
        'number',
        'qty_item',
        'qty',
        'status',
        'deliv_dest',
        'type',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outbound(): BelongsTo
    {
        return $this->belongsTo(Outbound::class, 'outbound_id');
    }
}
