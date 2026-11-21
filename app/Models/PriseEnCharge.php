<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PriseEnCharge extends Model
{
    use HasFactory;

    protected $table = 'prises_en_charge';
    protected $primaryKey = 'id_priseenche';

    protected $fillable = [
        'id_demande',
        'raison_medical',
        'ayant_droit',
        'type_prise',
        'justificatif_path',
        'exceptionnelle',
        'validee_par',
        'date_edition',
    ];

    protected function casts(): array
    {
        return [
            'date_edition'   => 'date',
            'exceptionnelle' => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'id_demande', 'id_demande');
    }

    public function agent()
    {
        return $this->hasOneThrough(
            Agent::class,
            Demande::class,
            'id_demande',
            'id_agent',
            'id_demande',
            'id_agent'
        );
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'validee_par', 'id');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getStatutDemAttribute(): string
    {
        return $this->demande?->statut_demande ?? 'En_attente';
    }

    /**
     * L'attestation est disponible lorsque le workflow est terminé :
     *  - PEC standard      : validée par le RH (statut « Validé »)
     *  - PEC exceptionnelle: approuvée par le DRH (statut « Approuvé »)
     */
    public function getAttestationDisponibleAttribute(): bool
    {
        $statut = $this->demande?->statut_demande;

        return $statut === 'Approuvé'
            || ($statut === 'Validé' && !$this->exceptionnelle);
    }

    /**
     * Vrai si la PEC exceptionnelle a été validée par le RH et attend le DRH.
     */
    public function getEnAttenteDrhAttribute(): bool
    {
        return $this->exceptionnelle && $this->demande?->statut_demande === 'Validé';
    }

    /**
     * Scope : PEC dont l'attestation est disponible (même règle que l'accessor).
     */
    public function scopeAttestationDisponible($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('demande', fn($d) => $d->where('statut_demande', 'Approuvé'))
              ->orWhere(function ($q2) {
                  $q2->where('exceptionnelle', false)
                     ->whereHas('demande', fn($d) => $d->where('statut_demande', 'Validé'));
              });
        });
    }

    public function getBeneficiaireLibelleAttribute(): string
    {
        return ucfirst($this->ayant_droit ?? 'Agent');
    }

    public function getJustificatifUrlAttribute(): ?string
    {
        if ($this->justificatif_path && Storage::disk('private')->exists($this->justificatif_path)) {
            return Storage::url($this->justificatif_path);
        }
        return null;
    }
}