<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Notifications\SmsSent;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\VonageMessage;

class SmsController extends Controller
{
    public function sendSms()
    {
        $user = Client::find(3);


        //$client->notify(new SmsSent('kjsdbjsjsjsksn'));

        // if you want to manage your secret, please do so by visiting your API Settings page in your dashboard
        $basic  = new \Vonage\Client\Credentials\Basic("4dd3b6a9", 'y28wpsV$Rj)gyjvMS');
        $client = new \Vonage\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($user->tel, "G Micro Service ", 'Toi là ' . $user->name . ' il faut payer l\'argent des gents hein  ')
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            return response()->json("ok");
        } else {
            return response()->json($message->getStatus());
        }
    }
}
