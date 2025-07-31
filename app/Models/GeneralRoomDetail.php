<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralRoomDetail extends Model
{
    protected $table = 'general_room_detail';
    protected $fillable = [
        'general_room_id',
        'product_id',
        'outbound_detail_id',
        'qty'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
