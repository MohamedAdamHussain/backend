<?php
namespace Modules\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\StockTransferItem;

class StockTransferItemFactory extends Factory
{
    protected $model = StockTransferItem::class;

    public function definition(): array
    {
        $quantity  = $this->faker->numberBetween(1, 100);
        $unitPrice = $this->faker->randomFloat(2, 10, 500);

        return [
            'stock_transfer_id' => StockTransfer::factory(),
            'product_id'        => Product::factory(),
            'quantity'          => $quantity,
            'unit_price'        => $unitPrice,
            'total_price'       => $quantity * $unitPrice,
        ];
    }
}
