<?php

namespace Modules\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\Warehouse;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name'     => $this->faker->company(),
            'location' => $this->faker->city(),
            'capacity' => $this->faker->numberBetween(100, 10000),
            'area'     => $this->faker->randomFloat(2, 50, 5000),
        ];
    }
}
