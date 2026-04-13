<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'payment_status',
        'paid_amount',
        'sale_order_id',
        'notes',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
    ];

    public function getTotalAmountAttribute()
    {
        return $this->saleOrder->total_amount;
    }

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
