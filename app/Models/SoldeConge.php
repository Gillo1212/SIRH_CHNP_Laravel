<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldeConge extends Model
{
    use HasFactory;

    protected $table = 'solde_conges';
    protected $primaryKey = 'id_solde';

    protected $fillable = [
        'id_agent',
        'id_type_conge',
        'annee',
        'solde_initial',
        'solde_pris',
        'solde_restant',
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    public function typeConge()
    {
        return $this->belongsTo(TypeConge::class, 'id_type_conge', 'id_type_conge');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeAnneeEnCours($query)
    {
        return $query->where('annee', date('Y'));
    }

    public function scopeParAgent($query, $agentId)
    {
        return $query->where('id_agent', $agentId);
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Mettre à jour le solde restant
     */
    public function calculerSoldeRestant()
    {
        $this->solde_restant = $this->solde_initial - $this->solde_pris;
        $this->save();
    }

    /**
     * Déduire des jours du solde
     */
    public function deduireJours($nbJours)
    {
        $this->solde_pris += $nbJours;
        $this->calculerSoldeRestant();
    }

    /**
     * Vérifier si le solde est suffisant
     */
    public function aSoldeSuffisant($nbJours)
    {
        return $this->solde_restant >= $nbJours;
    }
}