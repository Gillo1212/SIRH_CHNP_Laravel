<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriseEnCharge extends Model
{
    use HasFactory;

    protected $table = 'prises_en_charge';
    protected $primaryKey = 'id_priseenche';

    protected $fillable = [
        'id_demande',
        'raison_medical',
        'ayant_droit',
        'date_edition',
    ];

    protected function casts(): array
    {
        return [
            'date_edition' => 'date',
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
    // ACCESSORS
    // =====================================================

    public function getEstPourAgentAttribute()
    {
        return strtolower($this->ayant_droit) === 'agent';
    }

    public function getEstPourConjointAttribute()
    {
        return str_contains(strtolower($this->ayant_droit), 'conjoint');
    }

    public function getEstPourEnfantAttribute()
    {
        return str_contains(strtolower($this->ayant_droit), 'enfant');
    }
}