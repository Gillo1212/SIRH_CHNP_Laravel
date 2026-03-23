<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeConge extends Model
{
    use HasFactory;

    protected $table = 'type_conges';
    protected $primaryKey = 'id_type_conge';

    protected $fillable = [
        'libelle',
        'duree',
        'nb_jours_droit',
        'deductible',
    ];

    protected function casts(): array
    {
        return [
            'deductible' => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function conges()
    {
        return $this->hasMany(Conge::class, 'id_type_conge', 'id_type_conge');
    }

    public function soldeConges()
    {
        return $this->hasMany(SoldeConge::class, 'id_type_conge', 'id_type_conge');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeDeductible($query)
    {
        return $query->where('deductible', true);
    }

    public function scopeNonDeductible($query)
    {
        return $query->where('deductible', false);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstAdministratifAttribute()
    {
        return str_contains(strtolower($this->libelle), 'administratif');
    }

    public function getEstMaterniteAttribute()
    {
        return str_contains(strtolower($this->libelle), 'maternité');
    }
}