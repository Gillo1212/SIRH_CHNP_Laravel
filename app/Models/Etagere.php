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
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    /**
     * Dossiers contenus (composition)
     */
    public function dossiers()
    {
        return $this->hasMany(DossierAgent::class, 'id_etagere', 'id_etagere');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNombreDossiersAttribute()
    {
        return $this->dossiers()->count();
    }

    public function getReferenceCompleteAttribute()
    {
        return $this->numero ? "{$this->nom_etagere} - {$this->numero}" : $this->nom_etagere;
    }
}