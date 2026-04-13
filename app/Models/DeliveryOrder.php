<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'customer_id',
        'warehouse_id',
        'total_amount',
        'status',
        'notes'
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
}
