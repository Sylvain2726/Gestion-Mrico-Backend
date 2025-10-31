<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class SchedulerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que la tâche planifiée pour les échéances est configurée.
     */
    public function test_echeance_schedule_is_configured(): void
    {
        // Vérifier que la tâche est planifiée
        $events = Schedule::events();
        
        $this->assertCount(1, $events);
        
        $event = $events[0];
        $this->assertStringContainsString('prets:check-echeance', $event->command);
        $this->assertEquals('0 9 * * *', $event->expression); // Tous les jours à 9h00
    }

    /**
     * Test que la commande peut être exécutée via le scheduler.
     */
    public function test_scheduler_can_run_echeance_command(): void
    {
        // Exécuter toutes les tâches planifiées
        $this->artisan('schedule:run')
            ->assertExitCode(0);
    }
}
