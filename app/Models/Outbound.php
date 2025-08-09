<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    protected $table = 'outbound';
    protected $fillable = [
        'customer_id',
        'purc_doc',
        'sales_docs',
        'outbound_date',
        'number',
        'qty_item',
        'qty',
        'type',
        'status',
        'deliv_loc',
        'deliv_dest',
        'created_by',
        'note',
        'delivery_date',
        'delivery_note_number'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
