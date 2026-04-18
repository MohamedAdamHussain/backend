<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Database\Factories\WarehouseFactory;

class Warehouse extends Model
    {
        use HasFactory;

        /**
         * The attributes that are mass assignable.
         */
        protected $fillable = [
            'name',
            'location',
            'capacity',
            'area',
        ];

        public function products()
        {
            return $this->belongsToMany(Product::class)
                ->withPivot('quantity', 'low_stock_alert')
                ->withTimestamps();
        }

        public function supplierOrders()
        {
            return $this->hasMany(SupplyOrder::class);
        }
        public function stockTransfersFrom()
        {
            return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
        }
        public function stockTransfersTo()
        {
            return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
        }
        public function inventoryCounts()
        {
            return $this->hasMany(InventoryCount::class);
        }
        public function inventorytransactions()
        {
            return $this->hasMany(InventoryTransaction::class);
        }
        protected static function newFactory(): WarehouseFactory
        {
            return WarehouseFactory::new();
        }
    }
