<?php

namespace App\Models;

use App\Traits\Traits\LogsInventoryMovement;
use Illuminate\Database\Eloquent\Model;

class SupplyOrder extends Model
{
    use LogsInventoryMovement;

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'total_amount',
        'status',
        'notes'
    ];

    public function getInventoryMovementType()
    {
        return 'supply_order';
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(SupplyOrderItems::class);
    }
}
