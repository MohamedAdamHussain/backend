<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\InventoryLogFactory;

class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'date',
        'type',
        'related_type',
        'related_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'type' => 'string',
        'quantity' => 'integer',
        'related_id' => 'integer',
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function related()
    {
        return $this->morphTo();
    }
}
