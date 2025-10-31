<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pret>
 */
class PretFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_echeant' => $this->faker->dateTimeBetween('now', '+1 year'),
            'montant_total' => $this->faker->randomFloat(2, 1000, 50000),
            'client_id' => Client::factory(),
        ];
    }
}
