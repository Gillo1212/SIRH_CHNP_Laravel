<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Planning extends Model
{
    use HasFactory;

    protected $table = 'plannings';
    protected $primaryKey = 'id_planning';

    protected $fillable = [
        'id_service',
        'periode_debut',
        'periode_fin',
        'statut_planning',
        'motif_rejet',
        'date_creation',
    ];

    protected function casts(): array
    {
        return [
            'periode_debut' => 'date',
            'periode_fin' => 'date',
            'date_creation' => 'datetime',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    /**
     * Lignes du planning (composition)
     */
    public function lignes()
    {
        return $this->hasMany(LignePlanning::class, 'id_planning', 'id_planning');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeBrouillon($query)
    {
        return $query->where('statut_planning', 'Brouillon');
    }

    public function scopeTransmis($query)
    {
        return $query->where('statut_planning', 'Transmis');
    }

    public function scopeValide($query)
    {
        return $query->where('statut_planning', 'Validé');
    }

    public function scopeRejete($query)
    {
        return $query->where('statut_planning', 'Rejeté');
    }

    public function scopeParService($query, $serviceId)
    {
        return $query->where('id_service', $serviceId);
    }

    public function scopePeriodeEnCours($query)
    {
        $now = Carbon::now();
        return $query->where('periode_debut', '<=', $now)
                     ->where('periode_fin', '>=', $now);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstBrouillonAttribute()
    {
        return $this->statut_planning === 'Brouillon';
    }

    public function getEstValideAttribute()
    {
        return $this->statut_planning === 'Validé';
    }

    public function getEstModifiableAttribute()
    {
        return in_array($this->statut_planning, ['Brouillon', 'Rejeté']);
    }

    public function getDureeJoursAttribute()
    {
        return $this->periode_debut->diffInDays($this->periode_fin) + 1;
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Transmettre le planning à la RH
     */
    public function transmettre()
    {
        $this->update(['statut_planning' => 'Transmis']);
    }

    /**
     * Valider le planning (par RH)
     */
    public function valider()
    {
        $this->update(['statut_planning' => 'Validé']);
    }

    /**
     * Rejeter le planning (par RH)
     */
    public function rejeter($motif)
    {
        $this->update([
            'statut_planning' => 'Rejeté',
            'motif_rejet' => $motif,
        ]);
    }
}