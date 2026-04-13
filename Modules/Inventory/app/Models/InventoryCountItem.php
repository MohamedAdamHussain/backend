<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\InventoryCountItemFactory;

class InventoryCountItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'inventory_count_id',
        'product_id',
        'system_quantity',
        'actual_quantity',
        'difference',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'system_quantity' => 'integer',
        'actual_quantity' => 'integer',
        'difference' => 'integer',
    ];

    /**
     * Get the inventory count.
     */
    public function inventoryCount()
    {
        return $this->belongsTo(InventoryCount::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
