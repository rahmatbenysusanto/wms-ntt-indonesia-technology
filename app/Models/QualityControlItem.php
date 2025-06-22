<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControlItem extends Model
{
    protected $table = 'quality_control_item';
    protected $fillable = [
        'quality_control_detail_id',
        'purchase_order_detail_id',
        'qty'
    ];
}
