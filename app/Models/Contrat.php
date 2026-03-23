<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contrat extends Model
{
    use HasFactory;

    protected $table = 'contrats';
    protected $primaryKey = 'id_contrat';

    protected $fillable = [
        'id_agent',
        'date_debut',
        'date_fin',
        'salaire_base',
        'statut_contrat',
        'type_contrat',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'salaire_base' => 'decimal:2',
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
    // SCOPES
    // =====================================================

    public function scopeActif($query)
    {
        return $query->where('statut_contrat', 'Actif');
    }

    public function scopeExpirant($query, $jours = 60)
    {
        return $query->where('statut_contrat', 'Actif')
                     ->whereNotNull('date_fin')
                     ->whereBetween('date_fin', [
                         Carbon::now(),
                         Carbon::now()->addDays($jours)
                     ]);
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstExpirantAttribute()
    {
        if (!$this->date_fin) {
            return false; // CDI
        }

        return $this->date_fin->diffInDays(Carbon::now()) <= 60;
    }

    public function getJoursRestantsAttribute()
    {
        if (!$this->date_fin) {
            return null; // CDI
        }

        return Carbon::now()->diffInDays($this->date_fin, false);
    }
}