<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControlDetail extends Model
{
    protected $table = 'quality_control_detail';
    protected $fillable = [
        'quality_control_id',
        'purchase_order_detail_id',
        'qty',
        'status'
    ];
}
