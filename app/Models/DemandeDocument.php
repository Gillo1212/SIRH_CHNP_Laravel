<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeDocument extends Model
{
    use HasFactory;

    protected $table = 'demandes_documents';

    protected $fillable = [
        'agent_id',
        'type_document',
        'motif',
        'statut',
        'traite_par',
        'date_traitement',
        'fichier_genere',
        'motif_rejet',
    ];

    protected function casts(): array
    {
        return [
            'date_traitement' => 'datetime',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id_agent');
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par', 'id');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getLibelleTypeAttribute(): string
    {
        return match($this->type_document) {
            'attestation_travail' => 'Attestation de travail',
            'certificat_travail'  => 'Certificat de travail',
            'ordre_mission'       => 'Ordre de mission',
            default               => ucfirst(str_replace('_', ' ', $this->type_document)),
        };
    }

    public function getLibelleStatutAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'en_cours'   => 'En cours',
            'pret'       => 'Prêt',
            'rejete'     => 'Rejeté',
            default      => ucfirst($this->statut),
        };
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePret($query)
    {
        return $query->where('statut', 'pret');
    }
}
