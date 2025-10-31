<?php

namespace App\Console\Commands;

use App\Models\Pret;
use App\Notifications\EcheancePretNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckEcheancePrets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prets:check-echeance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les prêts à échéance dans 3 jours et envoie des notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Vérification des prêts à échéance...');

        // Date d'échéance dans 3 jours
        $dateEcheance = Carbon::now()->addDays(3)->toDateString();

        // Récupérer les prêts qui arrivent à échéance dans 3 jours et qui ne sont pas encore payés
        $pretsEcheance = Pret::where('date_echeant', $dateEcheance)
            ->where('montant_rest', '>', 0)
            ->get();

        if ($pretsEcheance->isEmpty()) {
            $this->info('Aucun prêt à échéance dans 3 jours.');
            return self::SUCCESS;
        }

        $this->info("Trouvé {$pretsEcheance->count()} prêt(s) à échéance dans 3 jours.");

        foreach ($pretsEcheance as $pret) {
            try {
                // Récupérer le client directement
                $client = \App\Models\Client::find($pret->client_id);
                
                if (!$client) {
                    $this->error("Client non trouvé pour le prêt #{$pret->id}");
                    continue;
                }
                
                // Envoyer la notification au client
                $client->notify(new EcheancePretNotification($pret));
                
                $this->line("Notification envoyée au client: {$client->name} (Prêt #{$pret->id})");
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'envoi de la notification pour le prêt #{$pret->id}: " . $e->getMessage());
            }
        }

        $this->info('Vérification terminée.');
        return self::SUCCESS;
    }
}
