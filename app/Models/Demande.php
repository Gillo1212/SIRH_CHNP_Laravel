<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $table = 'demandes';
    protected $primaryKey = 'id_demande';

    protected $fillable = [
        'id_agent',
        'type_demande',
        'statut_demande',
        'motif_refus',
        'date_traitement',
    ];

    protected function casts(): array
    {
        return [
            'date_demande' => 'datetime',
            'date_traitement' => 'datetime',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    /**
     * Relation polymorphe pour l'héritage
     */
    public function conge()
    {
        return $this->hasOne(Conge::class, 'id_demande', 'id_demande');
    }

    public function absence()
    {
        return $this->hasOne(Absence::class, 'id_demande', 'id_demande');
    }

    public function priseEnCharge()
    {
        return $this->hasOne(PriseEnCharge::class, 'id_demande', 'id_demande');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeEnAttente($query)
    {
        return $query->where('statut_demande', 'En_attente');
    }

    public function scopeValide($query)
    {
        return $query->where('statut_demande', 'Validé');
    }

    public function scopeApprouve($query)
    {
        return $query->where('statut_demande', 'Approuvé');
    }

    public function scopeRejete($query)
    {
        return $query->where('statut_demande', 'Rejeté');
    }

    public function scopeTypeConge($query)
    {
        return $query->where('type_demande', 'Conge');
    }

    public function scopeTypeAbsence($query)
    {
        return $query->where('type_demande', 'Absence');
    }
}