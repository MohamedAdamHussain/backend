<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'price_list_id',
    ];

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }
    public function saleOrders()
    {
        return $this->hasMany(SaleOrder::class);
    }
}
