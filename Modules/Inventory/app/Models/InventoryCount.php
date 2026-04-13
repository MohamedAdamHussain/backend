<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Inventory\Database\Factories\InventoryCountFactory;

class InventoryCount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'warehouse_id',
        'status',
        'notes',
    ];

    /**
     * Get the warehouse.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the count items.
     */
    public function items()
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    public function logs()
    {
        return $this->morphMany(InventoryLog::class, 'related');
    }
}
