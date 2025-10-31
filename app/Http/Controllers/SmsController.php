<?php

namespace App\Http\Controllers;

// Importation correcte de la classe Client de Twilio
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

// Les imports inutiles ont été retirés (AfricasTalking, App\Models\Client, VonageMessage, SmsSent)

class SmsController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Récupération des identifiants Twilio depuis le .env
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_NUMBER');

        // Vérification basique des identifiants
        if (! $sid || ! $token || ! $twilioNumber) {
            return response()->json([
                'statut' => 'Erreur de configuration',
                'message' => 'Les identifiants Twilio (SID, TOKEN, NUMBER) ne sont pas configurés dans votre fichier .env.',
            ], 500);
        }

        // 2. Initialisation de l'objet Twilio
        // NOTE : L'objet DOIT être instancié pour fonctionner.
        // Nous le nommons $twilioApi pour éviter le nom de variable '$client'.
        try {
            $twilioApi = new TwilioClient($sid, $token);
        } catch (\Exception $e) {
            Log::error('Twilio Init Error: '.$e->getMessage());

            return response()->json(['statut' => 'Erreur', 'message' => "Impossible d'initialiser l'API Twilio."], 500);
        }

        // 3. Définition du destinataire et du message
        // ATTENTION : Numéro au format international E.164 (+223xxxxxxxx)
        $numero_malien = '+22399023000';
        $message_a_envoyer = 'Bonjour du Mali! Ceci est un test Twilio réussi. Le temps est '.now()->format('H:i');

        try {
            // 4. Envoi du SMS (utilise $twilioApi au lieu de $client)
            $response = $twilioApi->messages->create(
                $numero_malien, // Numéro de téléphone du destinataire
                [
                    'from' => $twilioNumber, // Votre numéro Twilio
                    'body' => $message_a_envoyer, // Contenu du message
                ]
            );

            // Succès
            return response()->json([
                'statut' => 'Succès',
                'sid_message' => $response->sid, // Le SID de Twilio pour suivre le message
                'to' => $numero_malien,
            ]);
        } catch (\Exception $e) {
            // Échec
            Log::error("Erreur Twilio lors de l'envoi : ".$e->getMessage());

            return response()->json([
                'statut' => 'Erreur',
                'message' => 'Échec de l\'envoi du SMS : '.$e->getMessage(),
            ], 500);
        }
    }
}
