<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\SupplierOrderFactory;

class SupplyOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'total_amount',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_amount' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the supplier that owns the order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the warehouse that owns the order.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the order items.
     */
    public function items()
    {
        return $this->hasMany(SupplyOrderItem::class);
    }

    public function transactions()
    {
        return $this->morphMany(InventoryTransaction::class, 'related');
    }
}
