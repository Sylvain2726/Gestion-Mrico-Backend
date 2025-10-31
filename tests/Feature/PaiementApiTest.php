<?php

namespace Tests\Feature;

use App\Models\Paiement;
use App\Models\Pret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaiementApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_list_paiements(): void
    {
        $paiements = Paiement::factory()->count(3)->create();

        $response = $this->getJson('/api/paiements');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'montant_payer',
                        'mode_paiement',
                        'pret_id',
                        'created_at',
                        'updated_at',
                        'pret' => [
                            'id',
                            'date_echeant',
                            'montant_total',
                            'client_id',
                            'client',
                        ],
                    ],
                ],
            ]);
    }

    public function test_can_create_paiement(): void
    {
        $pret = Pret::factory()->create();
        $paiementData = [
            'montant_payer' => 1000.50,
            'mode_paiement' => 'especes',
            'pret_id' => $pret->id,
        ];

        $response = $this->postJson('/api/paiements', $paiementData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Paiement créé avec succès.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'montant_payer',
                    'mode_paiement',
                    'pret_id',
                    'created_at',
                    'updated_at',
                    'pret',
                ],
            ]);

        $this->assertDatabaseHas('paiements', $paiementData);
    }

    public function test_can_show_paiement(): void
    {
        $paiement = Paiement::factory()->create();

        $response = $this->getJson("/api/paiements/{$paiement->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $paiement->id,
                    'montant_payer' => $paiement->montant_payer,
                    'mode_paiement' => $paiement->mode_paiement,
                    'pret_id' => $paiement->pret_id,
                ],
            ]);
    }

    public function test_can_update_paiement(): void
    {
        $paiement = Paiement::factory()->create();
        $updateData = [
            'montant_payer' => 2000.75,
            'mode_paiement' => 'orange_money',
        ];

        $response = $this->putJson("/api/paiements/{$paiement->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Paiement mis à jour avec succès.',
            ]);

        $this->assertDatabaseHas('paiements', array_merge(['id' => $paiement->id], $updateData));
    }

    public function test_can_delete_paiement(): void
    {
        $paiement = Paiement::factory()->create();

        $response = $this->deleteJson("/api/paiements/{$paiement->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Paiement supprimé avec succès.',
            ]);

        $this->assertDatabaseMissing('paiements', ['id' => $paiement->id]);
    }

    public function test_can_get_paiements_by_pret(): void
    {
        $pret = Pret::factory()->create();
        $paiements = Paiement::factory()->count(2)->create(['pret_id' => $pret->id]);

        $response = $this->getJson("/api/paiements-pret/{$pret->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_validation_errors_for_invalid_data(): void
    {
        $response = $this->postJson('/api/paiements', [
            'montant_payer' => -100,
            'mode_paiement' => 'invalid_mode',
            'pret_id' => 999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['montant_payer', 'mode_paiement', 'pret_id']);
    }
}
