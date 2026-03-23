<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $table = 'absences';
    protected $primaryKey = 'id_absence';

    protected $fillable = [
        'id_demande',
        'date_absence',
        'type_absence',
        'justifie',
    ];

    protected function casts(): array
    {
        return [
            'date_absence' => 'date',
            'justifie' => 'boolean',
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
     * Pièces justificatives (composition)
     */
    public function piecesJustificatives()
    {
        return $this->hasMany(PieceJustificative::class, 'id_absence', 'id_absence');
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
    // SCOPES
    // =====================================================

    public function scopeJustifie($query)
    {
        return $query->where('justifie', true);
    }

    public function scopeNonJustifie($query)
    {
        return $query->where('justifie', false);
    }

    public function scopeMaladie($query)
    {
        return $query->where('type_absence', 'Maladie');
    }

    public function scopeInjustifiee($query)
    {
        return $query->where('type_absence', 'Injustifiée');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstMaladieAttribute()
    {
        return $this->type_absence === 'Maladie';
    }

    public function getEstInjustifieeAttribute()
    {
        return $this->type_absence === 'Injustifiée';
    }
}