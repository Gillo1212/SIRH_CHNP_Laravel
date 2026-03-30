<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etagere extends Model
{
    use HasFactory;

    protected $table = 'etageres';
    protected $primaryKey = 'id_etagere';

    protected $fillable = [
        'id_service',
        'nom_etagere',
        'numero',
        'reference',
        'description',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function dossiers()
    {
        return $this->hasMany(DossierAgent::class, 'id_etagere', 'id_etagere');
    }

    public function dossiersActifs()
    {
        return $this->hasMany(DossierAgent::class, 'id_etagere', 'id_etagere')
                    ->where('statut_da', 'Actif');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNombreDossiersAttribute(): int
    {
        return $this->dossiers()->count();
    }

    public function getNombreDocumentsAttribute(): int
    {
        return Document::whereIn('id_dossier',
            $this->dossiers()->pluck('id_dossier')
        )->where('statut_document', 'Actif')->count();
    }

    public function getReferenceCompleteAttribute(): string
    {
        return $this->numero ? "{$this->nom_etagere} — Étagère {$this->numero}" : $this->nom_etagere;
    }
}
