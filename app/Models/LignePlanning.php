<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LignePlanning extends Model
{
    use HasFactory;

    protected $table = 'ligne_plannings';
    protected $primaryKey = 'id_ligne';

    protected $fillable = [
        'id_planning',
        'id_agent',
        'id_typeposte',
        'date_poste',
        'heure_debut',
        'heure_fin',
    ];

    protected function casts(): array
    {
        return [
            'date_poste' => 'date',
            'heure_debut' => 'datetime:H:i',
            'heure_fin' => 'datetime:H:i',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function planning()
    {
        return $this->belongsTo(Planning::class, 'id_planning', 'id_planning');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    public function typePoste()
    {
        return $this->belongsTo(TypePoste::class, 'id_typeposte', 'id_typeposte');
    }

    /**
     * Heures supplémentaires générées (0..1)
     */
    public function heureSup()
    {
        return $this->hasOne(HeureSup::class, 'id_ligne', 'id_ligne');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNbHeuresAttribute()
    {
        $debut = Carbon::parse($this->heure_debut);
        $fin = Carbon::parse($this->heure_fin);
        
        // Si heure_fin < heure_debut, c'est un poste de nuit (J+1)
        if ($fin->lessThan($debut)) {
            $fin->addDay();
        }
        
        return $debut->diffInHours($fin, true);
    }

    public function getEstNuitAttribute()
    {
        return $this->typePoste->est_nuit;
    }

    public function getEstJourAttribute()
    {
        return $this->typePoste->est_jour;
    }

    public function getEstReposAttribute()
    {
        return $this->typePoste->est_repos;
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Calculer et créer les heures supplémentaires
     */
    public function calculerHeuresSup($tauxMajoration = 1.25)
    {
        $nbHeuresStandard = 8;
        $nbHeuresTravaillees = $this->nb_heures;
        
        if ($nbHeuresTravaillees > $nbHeuresStandard) {
            $nbHeuresSup = $nbHeuresTravaillees - $nbHeuresStandard;
            
            return HeureSup::create([
                'id_ligne' => $this->id_ligne,
                'nb_heures' => $nbHeuresSup,
                'taux' => $tauxMajoration,
                'montant' => $nbHeuresSup * $tauxMajoration * 5000, // Exemple: 5000 FCFA/heure
                'periode' => 'Trimestre',
                'statut_hs' => 'En_attente',
            ]);
        }
        
        return null;
    }
}