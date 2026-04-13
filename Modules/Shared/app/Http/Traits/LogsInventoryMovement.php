<?php
namespace Modules\shared\Http\Traits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Exceptions\InsufficientStockException;
use Modules\Inventory\Models\InventoryTransaction;

trait LogsInventoryMovement
{
    protected function logInventoryMovement(
        int $productId,
        int $warehouseId,
        string $type,
        int $quantity,  // سالب للخصم، موجب للإضافة
        Model $related
    ): void {
        InventoryTransaction::create([
            'product_id'   => $productId,
            'warehouse_id' => $warehouseId,
            'date'         => now(),
            'type'         => $type,
            'related_type' => get_class($related),
            'related_id'   => $related->id,
            'quantity'     => $quantity,
        ]);
    }

    protected function updateWarehouseStock(
        int $warehouseId,
        int $productId,
        int $quantity  // سالب للخصم، موجب للإضافة
    ): void {
        $currentStock = DB::table('product_warehouse')
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)->lockForUpdate()
            ->value('quantity') ?? 0;

        if ($currentStock + $quantity < 0) {
        throw new InsufficientStockException($productId, $warehouseId);
        }
        DB::table('product_warehouse')
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->increment('quantity', $quantity);
    }
}
