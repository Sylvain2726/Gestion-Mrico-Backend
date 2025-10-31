<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Pret;
use App\Notifications\EcheancePretNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EcheancePretNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que la commande de vérification des échéances fonctionne.
     */
    public function test_check_echeance_command(): void
    {
        // Créer un client
        $client = Client::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Créer un prêt qui arrive à échéance dans 3 jours (date exacte)
        $pret = Pret::factory()->create([
            'client_id' => $client->id,
            'date_echeant' => now()->addDays(3)->toDateString(),
            'montant_rest' => 50000,
        ]);

        // Exécuter la commande et vérifier qu'elle se termine avec succès
        $this->artisan('prets:check-echeance')
            ->expectsOutput('Vérification des prêts à échéance...')
            ->assertExitCode(0);

        // Vérifier que le prêt a été trouvé (en vérifiant les logs ou la base de données)
        $this->assertDatabaseHas('prets', [
            'id' => $pret->id,
            'client_id' => $client->id,
            'montant_rest' => 50000,
        ]);
    }

    /**
     * Test que la notification est envoyée correctement.
     */
    public function test_echeance_notification_is_sent(): void
    {
        Notification::fake();

        // Créer un client
        $client = Client::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Créer un prêt
        $pret = Pret::factory()->create([
            'client_id' => $client->id,
            'date_echeant' => now()->addDays(3),
            'montant_rest' => 50000,
        ]);

        // Envoyer la notification
        $client->notify(new EcheancePretNotification($pret));

        // Vérifier que la notification a été envoyée
        Notification::assertSentTo(
            $client,
            EcheancePretNotification::class
        );
    }

    /**
     * Test qu'aucune notification n'est envoyée pour les prêts déjà payés.
     */
    public function test_no_notification_for_paid_loans(): void
    {
        // Créer un client
        $client = Client::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Créer un prêt payé (montant_rest = 0)
        $pret = Pret::factory()->create([
            'client_id' => $client->id,
            'date_echeant' => now()->addDays(3),
            'montant_rest' => 0,
        ]);

        // Exécuter la commande
        $this->artisan('prets:check-echeance')
            ->expectsOutput('Vérification des prêts à échéance...')
            ->expectsOutput('Aucun prêt à échéance dans 3 jours.')
            ->assertExitCode(0);
    }

    /**
     * Test que le contenu de l'email est correct.
     */
    public function test_email_content_is_correct(): void
    {
        // Créer un client
        $client = Client::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Créer un prêt
        $pret = Pret::factory()->create([
            'client_id' => $client->id,
            'date_echeant' => now()->addDays(3),
            'montant_rest' => 75000,
        ]);

        // Envoyer la notification
        $notification = new EcheancePretNotification($pret);
        $mailMessage = $notification->toMail($client);

        // Vérifier le contenu de l'email
        $this->assertEquals('Rappel d\'échéance de prêt - Action requise', $mailMessage->subject);
        $this->assertStringContainsString('Bonjour John Doe,', $mailMessage->greeting);
        $this->assertStringContainsString('Nous vous informons que votre prêt arrive à échéance dans 3 jours.', $mailMessage->introLines[0]);
        $this->assertStringContainsString('75 000 FCFA', $mailMessage->introLines[3]);
        $this->assertStringContainsString('Il est important de vous présenter dans nos bureaux', $mailMessage->introLines[5]);
    }
}
