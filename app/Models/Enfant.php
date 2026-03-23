<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Enfant extends Model
{
    use HasFactory;

    protected $table = 'enfants';
    protected $primaryKey = 'id_enfant';

    protected $fillable = [
        'id_agent',
        'prenom_complet',
        'date_naissance_enfant',
        'lien_filiation',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance_enfant' => 'date',
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

    public function getAgeAttribute()
    {
        return $this->date_naissance_enfant->age;
    }

    public function getEstMineurAttribute()
    {
        return $this->age < 18;
    }

    public function getEstAChargeAttribute()
    {
        // Considéré à charge si moins de 21 ans
        return $this->age < 21;
    }
}