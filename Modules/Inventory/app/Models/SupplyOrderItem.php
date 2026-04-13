<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\SupplierOrderItemFactory;

class SupplyOrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'quantity',
        'unit_price',
        'total_price',
        'product_id',
        'supply_order_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the supply order that owns the item.
     */
    public function supplyOrder()
    {
        return $this->belongsTo(SupplyOrder::class);
    }
}
