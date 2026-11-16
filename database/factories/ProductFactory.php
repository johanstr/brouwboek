<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Gulle Gans ' . fake()->unique()->randomElement(['Blond', 'Dubbel', 'Tripel', 'Wit', 'IPA', 'Stout', 'Bok', 'Saison', 'Amber', 'Quadrupel']);

        return [
            'sku' => 'GG-' . strtoupper(fake()->unique()->bothify('???##')),
            'name' => $name,
            'style' => fake()->randomElement(['Blond', 'Dubbel', 'Tripel', 'Witbier', 'IPA', 'Stout']),
            'abv' => fake()->randomFloat(1, 4.5, 10),
            'volume_cl' => 33,
            'price' => fake()->randomFloat(2, 1.40, 2.20),
            'stock' => fake()->numberBetween(0, 400),
            'min_stock' => 48,
        ];
    }
}
