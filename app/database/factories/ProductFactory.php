<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $buyPrice = fake()->numberBetween(10000, 500000);

        return [
            'barcode' => fake()->unique()->ean13(),

            'name' => fake()->words(3, true),

            'category_id' => Category::inRandomOrder()->value('id'),

            'buy_price' => $buyPrice,

            'sell_price' => $buyPrice + fake()->numberBetween(1000, 50000),

            'stock' => fake()->numberBetween(0, 300),

            'unit' => 'عدد',

            'is_active' => true,
        ];
    }
}
