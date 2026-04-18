<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'production_date',
        'expiry_date',
    ];

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)
            ->withPivot('quantity', 'low_stock_alert')
            ->withTimestamps();
    }

    public function supplierOrderItems()
    {
        return $this->hasMany(SupplyOrderItem::class);
    }

    public function stockTransferItems()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function inventoryCountItems()
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    public function inventorytransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
