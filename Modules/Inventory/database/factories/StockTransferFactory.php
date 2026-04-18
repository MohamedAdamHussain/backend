<?php
namespace Modules\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\Warehouse;

class StockTransferFactory extends Factory
{
    protected $model = StockTransfer::class;

    public function definition(): array
    {
        return [
            'from_warehouse_id' => Warehouse::factory(),
            'to_warehouse_id'   => Warehouse::factory(),
            'status'            => 'pending',
            'total_items'       => null,
            'notes'             => $this->faker->sentence(),
        ];
    }

    // States — لتسهيل كتابة الـ tests
    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function inTransit(): static
    {
        return $this->state(['status' => 'in_transit']);
    }
}
