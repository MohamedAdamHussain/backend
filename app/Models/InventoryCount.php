<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCount extends Model
{
    protected $fillable = [
        'warehouse_id'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryCountItem::class);
    }
}
