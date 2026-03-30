<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $table = 'absences';
    protected $primaryKey = 'id_absence';

    protected $fillable = [
        'id_demande',
        'date_absence',
        'type_absence',
        'justifie',
    ];

    protected function casts(): array
    {
        return [
            'date_absence' => 'date',
            'justifie' => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'id_demande', 'id_demande');
    }

    /**
     * Pièces justificatives (composition)
     */
    public function piecesJustificatives()
    {
        return $this->hasMany(PieceJustificative::class, 'id_absence', 'id_absence');
    }

    /**
     * Accès direct à l'agent via la demande
     */
    public function agent()
    {
        return $this->hasOneThrough(
            User::class,
            Demande::class,
            'id_demande',
            'id',
            'id_demande',
            'id_agent'
        );
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeJustifie($query)
    {
        return $query->where('justifie', true);
    }

    public function scopeNonJustifie($query)
    {
        return $query->where('justifie', false);
    }

    public function scopeMaladie($query)
    {
        return $query->where('type_absence', 'Maladie');
    }

    public function scopeInjustifiee($query)
    {
        return $query->where('type_absence', 'Injustifiée');
    }

    /**
     * Filtrer les absences par service (via demande → agent)
     */
    public function scopeForService($query, int $serviceId)
    {
        return $query->whereHas('demande.agent', function ($q) use ($serviceId) {
            $q->where('id_service', $serviceId);
        });
    }

    /**
     * Filtrer les absences du service d'un manager
     */
    public function scopeForManager($query, int $userId)
    {
        $service = \App\Models\Service::where('id_agent_manager', $userId)->first();
        if (!$service) {
            return $query->whereRaw('1 = 0');
        }
        return $query->forService($service->id_service);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstMaladieAttribute()
    {
        return $this->type_absence === 'Maladie';
    }

    public function getEstInjustifieeAttribute()
    {
        return $this->type_absence === 'Injustifiée';
    }

    /**
     * Accès rapide au commentaire (stocké dans demande.motif_refus)
     */
    public function getCommentaireAttribute(): ?string
    {
        return $this->demande?->motif_refus;
    }

    /**
     * Filtrer par période
     */
    public function scopeForPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_absence', [$debut, $fin]);
    }

    /**
     * Filtrer par agent
     */
    public function scopeForAgent($query, int $agentId)
    {
        return $query->whereHas('demande', fn($q) => $q->where('id_agent', $agentId));
    }
}