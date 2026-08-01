<?php

namespace Database\Factories;

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
        $price = $this->faker->randomFloat(2, 15000, 120000);
        $discountPrice = $this->faker->boolean(40)
            ? max(0, $price - $this->faker->numberBetween(2000, 15000))
            : null;

        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(12),
            'price' => $price,
            'discount_price' => $discountPrice,
            'stock' => $this->faker->numberBetween(5, 120),
            'is_active' => $this->faker->boolean(90),
            'category_id' => null,
            'image' => 'products/' . $this->faker->unique()->word() . '.jpg',
        ];
    }
}
