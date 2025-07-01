<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    protected $table = 'outbound';
    protected $fillable = [
        'number',
        'purc_doc',
        'sales_doc',
        'client',
        'customer_id',
        'deliv_loc',
        'qty_item',
        'created_by'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
