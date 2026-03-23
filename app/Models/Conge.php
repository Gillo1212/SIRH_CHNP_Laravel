<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Conge extends Model
{
    use HasFactory;

    protected $table = 'conges';
    protected $primaryKey = 'id_conge';

    protected $fillable = [
        'id_demande',
        'id_type_conge',
        'date_debut',
        'date_fin',
        'nbres_jours',
        'date_approbation',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_approbation' => 'date',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function demande()
    {
        return $this->belongsTo(Demande::class, 'id_demande', 'id_demande');
    }

    public function typeConge()
    {
        return $this->belongsTo(TypeConge::class, 'id_type_conge', 'id_type_conge');
    }

    /**
     * Accès direct à l'agent via la demande
     */
    public function agent()
    {
        return $this->hasOneThrough(
            User::class,
            Demande::class,
            'id_demande', // FK sur demandes
            'id', // FK sur users
            'id_demande', // Local key sur conges
            'id_agent' // Local key sur demandes
        );
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstEnCoursAttribute()
    {
        $now = Carbon::now();
        return $now->between($this->date_debut, $this->date_fin);
    }

    public function getEstFuturAttribute()
    {
        return $this->date_debut->isFuture();
    }

    public function getEstPasseAttribute()
    {
        return $this->date_fin->isPast();
    }
}