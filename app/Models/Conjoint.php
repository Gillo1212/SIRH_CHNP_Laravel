<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conjoint extends Model
{
    use HasFactory;

    protected $table = 'conjoints';
    protected $primaryKey = 'id_conjoint';

    protected $fillable = [
        'id_agent',
        'nom_conj',
        'prenom_conj',
        'date_naissance_conj',
        'type_lien',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance_conj' => 'date',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNomCompletAttribute()
    {
        return $this->prenom_conj . ' ' . $this->nom_conj;
    }

    public function getAgeAttribute()
    {
        if (!$this->date_naissance_conj) {
            return null;
        }
        return $this->date_naissance_conj->age;
    }
}