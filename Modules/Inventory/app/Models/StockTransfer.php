<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Database\Factories\StockTransferFactory;

// use Modules\Inventory\Database\Factories\StockTransferFactory;

class StockTransfer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'total_items',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'string',
        'total_items' => 'integer',
    ];

    /**
     * Get the source warehouse.
     */
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Get the destination warehouse.
     */
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Get the transfer items.
     */
    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function transactions()
    {
        return $this->morphMany(InventoryTransaction::class, 'related');
    }
    protected static function newFactory(): StockTransferFactory
    {
        return StockTransferFactory::new();
    }
}
