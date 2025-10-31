<?php

namespace Database\Factories;

use App\Models\Pret;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'montant_payer' => $this->faker->randomFloat(2, 100, 10000),
            'mode_paiement' => $this->faker->randomElement(['especes', 'orange_money', 'moov_money']),
            'pret_id' => Pret::factory(),
        ];
    }
}
