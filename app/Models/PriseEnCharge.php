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
        'validee_par',
        'date_edition',
    ];

    protected function casts(): array
    {
        return [
            'date_edition'   => 'date',
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