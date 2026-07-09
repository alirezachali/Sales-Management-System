<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'نوشیدنی',
                'لبنیات',
                'تنقلات',
                'شوینده',
                'بهداشتی',
                'مواد غذایی',
                'کنسرو',
                'ادویه',
                'حبوبات'
            ]),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
