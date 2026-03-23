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
        'id_division',
        'id_agent_manager',
        'nom_service',
        'type_service',
        'tel_service',
        'nbre_agents',
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    /**
     * Division parente
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'id_division', 'id_division');
    }

    /**
     * Manager du service
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'id_agent_manager', 'id');
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
}