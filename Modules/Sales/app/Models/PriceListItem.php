<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Product;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'product_id',
        'special_price',
    ];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
