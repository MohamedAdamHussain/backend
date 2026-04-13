<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Product;

class DeliveryOrderItem extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
