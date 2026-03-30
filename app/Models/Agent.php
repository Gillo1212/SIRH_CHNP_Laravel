<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Agent extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, Notifiable;

    protected $primaryKey = 'id_agent';

    /**
     * Familles d'emploi disponibles.
     */
    public const FAMILLES_EMPLOI = [
        'Corps_Médical',
        'Corps_Paramédical',
        'Corps_Administratif',
        'Corps_Technique',
        'Corps_de_Soutien',
    ];

    protected $fillable = [
        'user_id',
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'situation_familiale',
        'nationalite',
        'adresse',            // AES-256
        'telephone',          // AES-256
        'email',
        'cni',                // AES-256 — Carte Nationale d'Identité
        'religion',
        'date_prise_service',
        'fontion',
        'grade',
        'categorie_cp',
        'famille_d_emploi',
        'statut_agent',
        'account_pending',
        'photo',
        'id_service',
        'id_division',
    ];

    protected function casts(): array
    {
        return [
            'adresse'           => 'encrypted', // Confidentialité CID
            'telephone'         => 'encrypted', // Confidentialité CID
            'cni'               => 'encrypted', // Confidentialité CID — CNI sensible
            'date_naissance'    => 'date',
            'date_prise_service'=> 'date',
            'account_pending'   => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'id_division', 'id_division');
    }

    public function enfants()
    {
        return $this->hasMany(Enfant::class, 'id_agent', 'id_agent');
    }

    public function conjoints()
    {
        return $this->hasMany(Conjoint::class, 'id_agent', 'id_agent');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'id_agent', 'id_agent');
    }

    public function contratActif()
    {
        return $this->hasOne(Contrat::class, 'id_agent', 'id_agent')
                    ->where('statut_contrat', 'Actif')
                    ->latest('date_debut');
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class, 'id_agent', 'id_agent');
    }

    public function mouvements()
    {
        return $this->hasMany(Mouvement::class, 'id_agent', 'id_agent');
    }

    public function soldeConges()
    {
        return $this->hasMany(SoldeConge::class, 'id_agent', 'id_agent');
    }

    public function dossier()
    {
        return $this->hasOne(DossierAgent::class, 'id_agent', 'id_agent');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Téléphone masqué pour affichage (Confidentialité CID).
     * Affiche les 4 premiers et 2 derniers chiffres.
     */
    public function getTelephoneMasqueAttribute(): string
    {
        $tel = $this->telephone;
        if (!$tel) return '—';
        return substr($tel, 0, 4) . str_repeat('X', max(0, strlen($tel) - 6)) . substr($tel, -2);
    }

    /**
     * CNI masquée pour affichage (Confidentialité CID).
     * Affiche les 2 premiers + XXXXX + 2 derniers caractères.
     */
    public function getCniMasqueAttribute(): string
    {
        $cni = $this->cni;
        if (!$cni) return '—';
        if (strlen($cni) <= 4) return str_repeat('X', strlen($cni));
        return substr($cni, 0, 2) . 'XXXXX' . substr($cni, -2);
    }

    /**
     * Route pour les notifications (requis par Notifiable).
     * L'email est désormais uniquement sur la table users.
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->email ?? $this->user?->email ?? null;
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActif($query)
    {
        return $query->where('statut_agent', 'Actif');
    }

    public function scopeDuService($query, int $serviceId)
    {
        return $query->where('id_service', $serviceId);
    }

    /**
     * Filtrer les agents gérés par un manager (son service).
     */
    public function scopeForManager($query, int $userId)
    {
        $service = \App\Models\Service::where('id_agent_manager', $userId)->first();
        if (!$service) {
            return $query->whereRaw('1 = 0');
        }
        return $query->where('id_service', $service->id_service);
    }

    public function scopeRecherche($query, ?string $terme)
    {
        if (!$terme) return $query;

        return $query->where(function ($q) use ($terme) {
            $q->where('nom', 'like', "%{$terme}%")
              ->orWhere('prenom', 'like', "%{$terme}%")
              ->orWhere('matricule', 'like', "%{$terme}%")
              ->orWhere('famille_d_emploi', 'like', "%{$terme}%");
        });
    }

    // =====================================================
    // ACTIVITY LOG
    // =====================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['matricule', 'nom', 'prenom', 'statut_agent', 'id_service', 'famille_d_emploi', 'fontion'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('agents');
    }
}
