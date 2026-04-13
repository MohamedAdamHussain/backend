<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Warehouse;

class SaleOrder extends Model
{
    protected $fillable = [
        'warehouse_id',
        'customer_id',
        'total_amount',
        'status',
        'payment_status',
        'amount_paid',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid'  => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function deliveryOrder()
    {
        return $this->hasOne(DeliveryOrder::class);
    }
}
