<?php
namespace Modules\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'            => $this->faker->word(),
            'description'     => $this->faker->sentence(),
            'price'           => $this->faker->randomFloat(2, 10, 1000),
            'production_date' => $this->faker->date(),
            'expiry_date'     => $this->faker->dateTimeBetween('+1 year', '+3 years'),
        ];
    }
}
