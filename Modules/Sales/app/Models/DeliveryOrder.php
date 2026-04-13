<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Warehouse;

class DeliveryOrder extends Model
{

    protected $fillable = [
        'customer_id',
        'warehouse_id',
        'total_amount',
        'status',
        'notes',
        'sale_order_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }
}
