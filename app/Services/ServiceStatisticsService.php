<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Absence;
use App\Models\Demande;
use App\Models\Planning;

/**
 * ServiceStatisticsService
 * Calcule les statistiques RH d'un service hospitalier.
 * Disponibilité CID : données toujours à jour, calcul optimisé.
 */
class ServiceStatisticsService
{
    public function getServiceStats(int $serviceId): array
    {
        $totalAgents  = Agent::where('id_service', $serviceId)->count();
        $activeAgents = Agent::where('id_service', $serviceId)->where('statut_agent', 'Actif')->count();

        $pendingLeaves = Demande::where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->whereHas('agent', fn($q) => $q->where('id_service', $serviceId))
            ->count();

        $approvedLeaves = Demande::where('type_demande', 'Conge')
            ->whereIn('statut_demande', ['Validé', 'Approuvé'])
            ->whereHas('agent', fn($q) => $q->where('id_service', $serviceId))
            ->whereMonth('created_at', now()->month)
            ->count();

        $currentMonthAbsences = Absence::forService($serviceId)
            ->whereHas('demande', fn($q) => $q->whereMonth('created_at', now()->month))
            ->count();

        $todayAbsences = Absence::forService($serviceId)
            ->where('date_absence', today())
            ->count();

        $attendanceRate = $this->calculateAttendanceRate($serviceId, $activeAgents);

        $pendingPlannings = Planning::where('id_service', $serviceId)
            ->where('statut_planning', 'Transmis')
            ->count();

        return [
            'total_agents'           => $totalAgents,
            'active_agents'          => $activeAgents,
            'inactive_agents'        => $totalAgents - $activeAgents,
            'pending_leaves'         => $pendingLeaves,
            'approved_leaves'        => $approvedLeaves,
            'current_month_absences' => $currentMonthAbsences,
            'today_absences'         => $todayAbsences,
            'attendance_rate'        => round($attendanceRate, 1),
            'pending_plannings'      => $pendingPlannings,
        ];
    }

    public function getAbsencesByMonth(int $serviceId, int $months = 6): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $count = Absence::forService($serviceId)
                ->whereHas('demande', fn($q) => $q->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month))
                ->count();
            $data[] = [
                'label' => $date->isoFormat('MMM'),
                'count' => $count,
            ];
        }
        return $data;
    }

    public function getAgentsByStatut(int $serviceId): array
    {
        return Agent::where('id_service', $serviceId)
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();
    }

    protected function calculateAttendanceRate(int $serviceId, int $activeAgents): float
    {
        if ($activeAgents === 0) return 0;

        $workingDays = $this->getWorkingDaysThisMonth();
        $absences    = Absence::forService($serviceId)
            ->whereHas('demande', fn($q) => $q->whereMonth('created_at', now()->month))
            ->count();

        $expected = $activeAgents * $workingDays;
        $actual   = max(0, $expected - $absences);

        return ($actual / $expected) * 100;
    }

    protected function getWorkingDaysThisMonth(): int
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();
        $days  = 0;

        while ($start->lte($end)) {
            if (!$start->isWeekend()) {
                $days++;
            }
            $start->addDay();
        }

        return $days;
    }
}
