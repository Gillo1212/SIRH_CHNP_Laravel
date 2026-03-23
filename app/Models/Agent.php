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
        'adresse',        // AES-256
        'telephone',      // AES-256
        'email',
        'date_recrutement',
        'fonction',
        'grade',
        'categorie_cp',
        'numero_assurance', // AES-256
        'statut',
        'account_pending',
        'photo',
        'id_service',
        'id_division',
    ];

    protected function casts(): array
    {
        return [
            'adresse'          => 'encrypted', // Confidentialité CID
            'telephone'        => 'encrypted', // Confidentialité CID
            'numero_assurance' => 'encrypted', // Confidentialité CID
            'account_pending'   => 'boolean',
            'date_naissance'   => 'date',
            'date_recrutement' => 'date',
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
     * Téléphone masqué pour affichage (Confidentialité)
     */
    public function getTelephoneMasqueAttribute(): string
    {
        $tel = $this->telephone;
        if (!$tel) return '—';
        // Affiche les 2 premiers et 2 derniers chiffres
        return substr($tel, 0, 4) . str_repeat('X', max(0, strlen($tel) - 6)) . substr($tel, -2);
    }

    /**
     * Adresse masquée pour affichage (Confidentialité)
     */
    /**
     * Route pour les notifications email (requis par Notifiable)
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }

    public function getAdresseMasqueeAttribute(): string
    {
        $addr = $this->adresse;
        if (!$addr) return '—';
        return substr($addr, 0, 15) . '...';
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActif($query)
    {
        return $query->where('statut', 'Actif');
    }

    public function scopeDuService($query, int $serviceId)
    {
        return $query->where('id_service', $serviceId);
    }

    public function scopeRecherche($query, ?string $terme)
    {
        if (!$terme) return $query;

        return $query->where(function ($q) use ($terme) {
            $q->where('nom', 'like', "%{$terme}%")
              ->orWhere('prenom', 'like', "%{$terme}%")
              ->orWhere('matricule', 'like', "%{$terme}%")
              ->orWhere('fonction', 'like', "%{$terme}%");
        });
    }

    // =====================================================
    // ACTIVITY LOG
    // =====================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['matricule', 'nom', 'prenom', 'statut', 'id_service', 'fonction'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('agents');
    }
}
