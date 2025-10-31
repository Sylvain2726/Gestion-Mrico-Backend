<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planifier la vérification des prêts à échéance tous les jours à 9h00
Schedule::command('prets:check-echeance')
    ->dailyAt('09:00')
    ->description('Vérifier les prêts à échéance dans 3 jours et envoyer des notifications');
