<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePoste extends Model
{
    use HasFactory;

    protected $table = 'type_postes';
    protected $primaryKey = 'id_typeposte';

    protected $fillable = [
        'libelle',
        'description',
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    public function lignePlannings()
    {
        return $this->hasMany(LignePlanning::class, 'id_typeposte', 'id_typeposte');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstJourAttribute()
    {
        return $this->libelle === 'Jour';
    }

    public function getEstNuitAttribute()
    {
        return $this->libelle === 'Nuit';
    }

    public function getEstReposAttribute()
    {
        return $this->libelle === 'Repos';
    }

    public function getEstGardeAttribute()
    {
        return $this->libelle === 'Garde';
    }
}