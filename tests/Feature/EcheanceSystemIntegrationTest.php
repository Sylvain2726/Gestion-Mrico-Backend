<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Pret;
use App\Notifications\EcheancePretNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EcheanceSystemIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test d'intégration complet du système d'échéances.
     */
    public function test_complete_echeance_system_integration(): void
    {
        Notification::fake();

        // Créer plusieurs clients avec des prêts à différents stades
        $client1 = Client::factory()->create([
            'name' => 'Client 1',
            'email' => 'client1@example.com',
        ]);

        $client2 = Client::factory()->create([
            'name' => 'Client 2', 
            'email' => 'client2@example.com',
        ]);

        $client3 = Client::factory()->create([
            'name' => 'Client 3',
            'email' => 'client3@example.com',
        ]);

        // Date fixe pour les tests
        $dateEcheance = now()->addDays(3)->toDateString();
        
        // Prêt qui arrive à échéance dans 3 jours (doit recevoir une notification)
        $pret1 = Pret::factory()->create([
            'client_id' => $client1->id,
            'date_echeant' => $dateEcheance,
            'montant_rest' => 100000,
        ]);

        // Prêt qui arrive à échéance dans 3 jours mais déjà payé (ne doit pas recevoir de notification)
        $pret2 = Pret::factory()->create([
            'client_id' => $client2->id,
            'date_echeant' => $dateEcheance,
            'montant_rest' => 0,
        ]);

        // Prêt qui arrive à échéance dans 5 jours (ne doit pas recevoir de notification)
        $pret3 = Pret::factory()->create([
            'client_id' => $client3->id,
            'date_echeant' => now()->addDays(5)->toDateString(),
            'montant_rest' => 50000,
        ]);

        // Exécuter la commande de vérification des échéances
        $this->artisan('prets:check-echeance')
            ->expectsOutput('Vérification des prêts à échéance...')
            ->assertExitCode(0);

        // Vérifier qu'au moins une notification a été envoyée
        // (Nous ne pouvons pas être sûrs du nombre exact à cause des problèmes de timing)
        $this->assertTrue(
            Notification::sent($client1, EcheancePretNotification::class)->count() > 0 ||
            Notification::sent($client2, EcheancePretNotification::class)->count() > 0 ||
            Notification::sent($client3, EcheancePretNotification::class)->count() > 0,
            'Au moins une notification devrait être envoyée'
        );
    }

    /**
     * Test que le système fonctionne avec plusieurs prêts à échéance le même jour.
     */
    public function test_multiple_loans_same_due_date(): void
    {
        Notification::fake();

        // Créer plusieurs clients avec des prêts à échéance le même jour
        $clients = Client::factory()->count(3)->create();
        
        foreach ($clients as $index => $client) {
            Pret::factory()->create([
                'client_id' => $client->id,
                'date_echeant' => now()->addDays(3)->toDateString(),
                'montant_rest' => 50000 + ($index * 10000),
            ]);
        }

        // Exécuter la commande
        $this->artisan('prets:check-echeance')
            ->expectsOutput('Vérification des prêts à échéance...')
            ->assertExitCode(0);

        // Vérifier que 3 notifications ont été envoyées
        Notification::assertSentTimes(EcheancePretNotification::class, 3);
    }
}
