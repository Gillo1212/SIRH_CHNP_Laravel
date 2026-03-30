<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mouvement extends Model
{
    use HasFactory, LogsActivity;

    protected $table      = 'mouvements';
    protected $primaryKey = 'id_mouvement';

    protected $fillable = [
        'id_agent', 'id_service', 'id_service_origine',
        'date_mouvement', 'type_mouvement', 'motif',
        'statut', 'cree_par', 'valide_par', 'date_validation',
        'decision_generee', 'signe_par', 'date_signature',
    ];

    protected function casts(): array
    {
        return [
            'date_mouvement'  => 'date',
            'date_validation' => 'datetime',
            'date_signature'  => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────────
    // AUDIT — Intégrité CID
    // ──────────────────────────────────────────────────
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type_mouvement', 'statut', 'id_service', 'id_service_origine', 'motif'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ──────────────────────────────────────────────────
    // CONSTANTES
    // ──────────────────────────────────────────────────
    public const TYPES = [
        'Affectation initiale' => [
            'label' => 'Affectation initiale', 'icon' => 'fa-user-plus',
            'color' => '#1565C0', 'bg' => '#DBEAFE',
            'description' => "Première affectation de l'agent dans un service",
        ],
        'Mutation' => [
            'label' => 'Mutation', 'icon' => 'fa-exchange-alt',
            'color' => '#D97706', 'bg' => '#FEF3C7',
            'description' => 'Transfert vers un autre service du CHNP',
        ],
        'Retour' => [
            'label' => 'Retour / Réintégration', 'icon' => 'fa-undo-alt',
            'color' => '#059669', 'bg' => '#D1FAE5',
            'description' => 'Réintégration après absence prolongée ou détachement',
        ],
        'Départ' => [
            'label' => 'Départ', 'icon' => 'fa-sign-out-alt',
            'color' => '#DC2626', 'bg' => '#FEE2E2',
            'description' => 'Démission, retraite, fin de contrat ou décès',
        ],
    ];

    public const STATUTS = [
        'en_attente' => ['label' => 'En attente',  'color' => '#D97706', 'bg' => '#FEF3C7', 'icon' => 'fa-clock'],
        'valide_drh' => ['label' => 'Validé DRH',  'color' => '#059669', 'bg' => '#D1FAE5', 'icon' => 'fa-check-double'],
        'effectue'   => ['label' => 'Effectué',    'color' => '#1565C0', 'bg' => '#DBEAFE', 'icon' => 'fa-check-circle'],
        'annule'     => ['label' => 'Annulé',      'color' => '#6B7280', 'bg' => '#F3F4F6', 'icon' => 'fa-times-circle'],
    ];

    public const SOUS_TYPES_DEPART = [
        'Démission'      => 'Démission volontaire',
        'Retraite'       => 'Départ à la retraite',
        'Fin de contrat' => 'Fin de contrat (non renouvellement)',
        'Licenciement'   => 'Licenciement',
        'Décès'          => 'Décès',
        'Détachement'    => 'Détachement (mise à disposition)',
    ];

    // ──────────────────────────────────────────────────
    // RELATIONS
    // ──────────────────────────────────────────────────
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    /** Service de destination */
    public function serviceDestination()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    /** Service d'origine */
    public function serviceOrigine()
    {
        return $this->belongsTo(Service::class, 'id_service_origine', 'id_service');
    }

    /** Alias pour compatibilité */
    public function service()
    {
        return $this->serviceDestination();
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function signataire()
    {
        return $this->belongsTo(User::class, 'signe_par');
    }

    // ──────────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────────
    public function scopeParAgent($query, $agentId)
    {
        return $query->where('id_agent', $agentId);
    }

    public function scopeParService($query, $serviceId)
    {
        return $query->where(function ($q) use ($serviceId) {
            $q->where('id_service', $serviceId)
              ->orWhere('id_service_origine', $serviceId);
        });
    }

    public function scopeParType($query, string $type)
    {
        return $query->where('type_mouvement', $type);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeValideDRH($query)
    {
        return $query->where('statut', 'valide_drh');
    }

    public function scopeEffectue($query)
    {
        return $query->where('statut', 'effectue');
    }

    public function scopePendingValidation($query)
    {
        return $query->whereIn('statut', ['en_attente', 'valide_drh']);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('date_mouvement');
    }

    // ──────────────────────────────────────────────────
    // ACCESSORS
    // ──────────────────────────────────────────────────
    public function getEstModifiableAttribute(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function getEstAnnulableAttribute(): bool
    {
        return in_array($this->statut, ['en_attente', 'valide_drh']);
    }

    public function getEstValidableAttribute(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function getEstEffectuableAttribute(): bool
    {
        return $this->statut === 'valide_drh';
    }

    public function getCouleurTypeAttribute(): array
    {
        return self::TYPES[$this->type_mouvement]
            ?? ['color' => '#6B7280', 'bg' => '#F3F4F6', 'icon' => 'fa-question', 'label' => $this->type_mouvement];
    }

    public function getCouleurStatutAttribute(): array
    {
        return self::STATUTS[$this->statut]
            ?? ['color' => '#6B7280', 'bg' => '#F3F4F6', 'icon' => 'fa-question', 'label' => $this->statut];
    }
}
