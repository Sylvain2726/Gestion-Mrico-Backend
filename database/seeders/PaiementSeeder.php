<?php

namespace Database\Seeders;

use App\Models\Paiement;
use App\Models\Pret;
use Illuminate\Database\Seeder;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des paiements pour les prêts existants
        $prets = Pret::all();

        foreach ($prets as $pret) {
            // Créer 1 à 3 paiements par prêt
            $nombrePaiements = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $nombrePaiements; $i++) {
                Paiement::factory()->create([
                    'pret_id' => $pret->id,
                    'montant_payer' => fake()->randomFloat(2, 100, $pret->montant_total / 2),
                ]);
            }
        }
    }
}
