<?php

use App\Console\Commands\VerifierExpirationContrats;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// PLANIFICATION AUTOMATIQUE — Disponibilité CID
// ============================================================

/**
 * Vérification quotidienne des expirations de contrats.
 * Exécutée chaque jour à 06h00 en production.
 * - Marque les contrats expirés (Intégrité)
 * - Notifie les agents RH (Disponibilité)
 * - Journalise dans l'audit trail (Confidentialité)
 */
Schedule::command(VerifierExpirationContrats::class)
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/contrats-expiration.log'))
    ->description('Vérification quotidienne expirations contrats');
