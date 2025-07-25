<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderEditReq extends Model
{
    protected $table = 'purchase_order_edit_req';
    protected $fillable = [
        'purchase_order_id',
        'request_by',
        'type',
        'note',
        'status',
        'details',
        'approved_by',
        'approved_at',
    ];

    public function requestBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    public function approvedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
