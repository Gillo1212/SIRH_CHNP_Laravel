<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contrat extends Model
{
    use HasFactory, LogsActivity;

    protected $table      = 'contrats';
    protected $primaryKey = 'id_contrat';

    protected $fillable = [
        'id_agent',
        'date_debut',
        'date_fin',
        'statut_contrat',
        'type_contrat',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin'   => 'date',
        ];
    }

    // =====================================================
    // AUDIT (Intégrité CID)
    // =====================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type_contrat', 'statut_contrat', 'date_debut', 'date_fin'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // =====================================================
    // CONSTANTES
    // =====================================================

    public const TYPES = [
        'PE'        => 'Poste d\'Emploi (PE)',
        'PCH'       => 'Personnel Contractuel (PCH)',
        'PU'        => 'Praticien Universitaire (PU)',
        'Vacataire' => 'Vacataire',
        'CMSAS'     => 'CMSAS',
        'Interne'   => 'Interne',
        'Stagiaire' => 'Stagiaire',
    ];

    public const STATUTS = [
        'Actif'             => ['label' => 'Actif',              'color' => '#10B981', 'bg' => '#D1FAE5'],
        'Expiré'            => ['label' => 'Expiré',             'color' => '#DC2626', 'bg' => '#FEE2E2'],
        'Clôturé'           => ['label' => 'Clôturé',            'color' => '#6B7280', 'bg' => '#F3F4F6'],
        'En_renouvellement' => ['label' => 'En renouvellement',  'color' => '#D97706', 'bg' => '#FEF3C7'],
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActif($query)
    {
        return $query->where('statut_contrat', 'Actif');
    }

    public function scopeExpirant($query, int $jours = 60)
    {
        return $query->where('statut_contrat', 'Actif')
                     ->whereNotNull('date_fin')
                     ->whereBetween('date_fin', [
                         Carbon::now()->startOfDay(),
                         Carbon::now()->addDays($jours)->endOfDay(),
                     ]);
    }

    public function scopeExpire($query)
    {
        return $query->where('statut_contrat', 'Actif')
                     ->whereNotNull('date_fin')
                     ->where('date_fin', '<', Carbon::now()->startOfDay());
    }

    public function scopeParType($query, string $type)
    {
        return $query->where('type_contrat', $type);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstExpirantAttribute(): bool
    {
        if (!$this->date_fin) {
            return false;
        }
        return $this->statut_contrat === 'Actif'
            && $this->date_fin->isFuture()
            && $this->date_fin->diffInDays(Carbon::now()) <= 60;
    }

    public function getEstExpireAttribute(): bool
    {
        if (!$this->date_fin) {
            return false;
        }
        return $this->date_fin->isPast();
    }

    public function getJoursRestantsAttribute(): ?int
    {
        if (!$this->date_fin) {
            return null;
        }
        return (int) Carbon::now()->diffInDays($this->date_fin, false);
    }

    public function getDureeAttribute(): string
    {
        if (!$this->date_fin) {
            return 'Indéterminée (CDI)';
        }
        $mois = (int) $this->date_debut->diffInMonths($this->date_fin);
        if ($mois < 1) {
            return $this->date_debut->diffInDays($this->date_fin) . ' jours';
        }
        if ($mois < 12) {
            return $mois . ' mois';
        }
        $annees = (int) floor($mois / 12);
        $reste  = $mois % 12;
        return $annees . ' an' . ($annees > 1 ? 's' : '') . ($reste ? " {$reste} mois" : '');
    }

    public function getLibelleTypeAttribute(): string
    {
        return self::TYPES[$this->type_contrat] ?? $this->type_contrat;
    }

    public function getUrgenceAttribute(): string
    {
        $j = $this->jours_restants;
        if ($j === null) return 'none';
        if ($j <= 0)   return 'expired';
        if ($j <= 7)   return 'critical';
        if ($j <= 15)  return 'high';
        if ($j <= 30)  return 'medium';
        if ($j <= 60)  return 'low';
        return 'none';
    }
}
