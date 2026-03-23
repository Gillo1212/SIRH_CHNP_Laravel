<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeureSup extends Model
{
    use HasFactory;

    protected $table = 'heures_sup';
    protected $primaryKey = 'id_hsup';

    protected $fillable = [
        'id_ligne',
        'nb_heures',
        'taux',
        'montant',
        'periode',
        'statut_hs',
    ];

    protected function casts(): array
    {
        return [
            'nb_heures' => 'decimal:2',
            'taux' => 'decimal:2',
            'montant' => 'decimal:2',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function lignePlanning()
    {
        return $this->belongsTo(LignePlanning::class, 'id_ligne', 'id_ligne');
    }

    /**
     * Accès direct à l'agent
     */
    public function agent()
    {
        return $this->hasOneThrough(
            User::class,
            LignePlanning::class,
            'id_ligne',
            'id',
            'id_ligne',
            'id_agent'
        );
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeEnAttente($query)
    {
        return $query->where('statut_hs', 'En_attente');
    }

    public function scopeValide($query)
    {
        return $query->where('statut_hs', 'Validé');
    }

    public function scopePaye($query)
    {
        return $query->where('statut_hs', 'Payé');
    }

    public function scopeTrimestre($query)
    {
        return $query->where('periode', 'Trimestre');
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Valider les heures sup
     */
    public function valider()
    {
        $this->update(['statut_hs' => 'Validé']);
    }

    /**
     * Marquer comme payé
     */
    public function marquerPaye()
    {
        $this->update(['statut_hs' => 'Payé']);
    }

    /**
     * Recalculer le montant
     */
    public function recalculerMontant($tauxHoraire = 5000)
    {
        $montant = $this->nb_heures * $this->taux * $tauxHoraire;
        $this->update(['montant' => $montant]);
    }
}