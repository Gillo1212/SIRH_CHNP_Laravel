<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $primaryKey = 'id_service';

    protected $fillable = [
        'id_agent_manager',
        'id_agent_major',
        'nom_service',
        'type_service',
        'tel_service',
        'nbre_agents',
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    /**
     * Divisions du service (agrégation)
     */
    public function divisions()
    {
        return $this->hasMany(Division::class, 'id_service', 'id_service');
    }

    /**
     * Manager du service
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'id_agent_manager', 'id');
    }

    /**
     * Major du service (responsable paramédical)
     */
    public function major()
    {
        return $this->belongsTo(User::class, 'id_agent_major', 'id');
    }

    /**
     * Agents du service
     */
    public function agents()
    {
        return $this->hasMany(Agent::class, 'id_service', 'id_service');
    }

    /**
     * Mouvements liés au service
     */
    public function mouvements()
    {
        return $this->hasMany(Mouvement::class, 'id_service', 'id_service');
    }

    /**
     * Plannings du service
     */
    public function plannings()
    {
        return $this->hasMany(Planning::class, 'id_service', 'id_service');
    }

    /**
     * Étagères (GED) du service
     */
    public function etageres()
    {
        return $this->hasMany(Etagere::class, 'id_service', 'id_service');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeClinique($query)
    {
        return $query->where('type_service', 'Clinique');
    }

    public function scopeAdministratif($query)
    {
        return $query->where('type_service', 'Administratif');
    }

    /**
     * Service(s) gérés par cet utilisateur (manager)
     */
    public function scopeForManager($query, int $userId)
    {
        return $query->where('id_agent_manager', $userId);
    }

    /**
     * Service(s) gérés par cet utilisateur (major)
     */
    public function scopeForMajor($query, int $userId)
    {
        return $query->where('id_agent_major', $userId);
    }

    // =====================================================
    // MÉTHODES STATISTIQUES
    // =====================================================

    public function getActiveAgentsCountAttribute(): int
    {
        return $this->agents()->where('statut_agent', 'Actif')->count();
    }

    public function getPendingLeavesCountAttribute(): int
    {
        return \App\Models\Demande::where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->whereHas('agent', fn($q) => $q->where('id_service', $this->id_service))
            ->count();
    }

    public function getCurrentMonthAbsencesCountAttribute(): int
    {
        return \App\Models\Absence::whereHas('demande.agent', fn($q) => $q->where('id_service', $this->id_service))
            ->whereHas('demande', fn($q) => $q->whereMonth('created_at', now()->month))
            ->count();
    }
}