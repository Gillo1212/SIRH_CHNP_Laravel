<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'login',
        'email',
        'password',
        'statut_compte',
        'verouille',
        'tentatives_connexion',
        'derniere_connexion',
        'agent_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'          => 'hashed',
            'derniere_connexion' => 'datetime',
            'verouille'         => 'boolean',
            'agent_completed'   => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    /**
     * Préférences utilisateur
     */
    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Tickets support créés par l'utilisateur
     */
    public function ticketsSupport()
    {
        return $this->hasMany(TicketSupport::class);
    }

    /**
     * Données RH de l'agent (relation 1-1)
     */
    public function agent()
    {
        return $this->hasOne(Agent::class, 'user_id', 'id');
    }

    /**
     * Services dont cet utilisateur est manager
     */
    public function servicesGeres()
    {
        return $this->hasMany(Service::class, 'id_agent_manager');
    }

    /**
     * Logs d'audit créés par cet utilisateur
     */
    public function logsAudit()
    {
        return $this->hasMany(LogAudit::class, 'id_utilisateur');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Nom complet via la relation agent
     */
    public function getNomCompletAttribute(): string
    {
        return $this->agent
            ? $this->agent->prenom . ' ' . $this->agent->nom
            : ($this->name ?? 'Utilisateur');
    }

    // =====================================================
    // GESTION CONNEXION & SÉCURITÉ
    // =====================================================

    public static function validatePasswordComplexity(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
        }

        return $errors;
    }

    public function incrementLoginAttempts(): void
    {
        $this->increment('tentatives_connexion');

        if ($this->tentatives_connexion >= 5) {
            $this->verouille = true;
            $this->statut_compte = 'Suspendu';
            $this->save();

            activity()->causedBy($this)->log("Compte verrouillé après 5 tentatives échouées");
        }
    }

    public function resetLoginAttempts(): void
    {
        $this->tentatives_connexion = 0;
        $this->derniere_connexion = now();
        $this->save();
    }

    public function estVerouille(): bool
    {
        return (bool) $this->verouille;
    }

    public function estActif(): bool
    {
        return $this->statut_compte === 'Actif';
    }

    public function estSuspendu(): bool
    {
        return $this->statut_compte === 'Suspendu';
    }

    public function deverouiller(): void
    {
        $this->verouille = false;
        $this->tentatives_connexion = 0;
        $this->statut_compte = 'Actif';
        $this->save();

        activity()->causedBy(Auth::user())->performedOn($this)->log("Compte déverrouillé par un administrateur");
    }

    public function suspendre(?string $motif = null): void
    {
        $this->statut_compte = 'Suspendu';
        $this->save();

        activity()->causedBy(Auth::user())->performedOn($this)
            ->withProperties(['motif' => $motif])->log("Compte suspendu");
    }

    public function desactiver(): void
    {
        $this->statut_compte = 'Inactif';
        $this->save();

        activity()->causedBy(Auth::user())->performedOn($this)->log("Compte désactivé");
    }

    public function activer(): void
    {
        $this->statut_compte = 'Actif';
        $this->verouille = false;
        $this->tentatives_connexion = 0;
        $this->save();

        activity()->causedBy(Auth::user())->performedOn($this)->log("Compte activé");
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActif($query)
    {
        return $query->where('statut_compte', 'Actif')->where('verouille', false);
    }

    public function scopeCompteActif($query)
    {
        return $query->where('statut_compte', 'Actif')->where('verouille', false);
    }

    public function scopeVerouille($query)
    {
        return $query->where('verouille', true);
    }

    public function scopeSuspendu($query)
    {
        return $query->where('statut_compte', 'Suspendu');
    }

    // =====================================================
    // ACTIVITY LOG
    // =====================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['login', 'statut_compte', 'verouille'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
