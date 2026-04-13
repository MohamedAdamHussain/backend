<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Product;

class SaleOrderItem extends Model
{
    protected $fillable = [
        'sale_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'total_price',
    ];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
