<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    use HasFactory;

    protected $table = 'mouvements';
    protected $primaryKey = 'id_mouvement';

    protected $fillable = [
        'id_agent',
        'id_service',
        'date_mouvement',
        'type_mouvement',
        'motif',
    ];

    protected function casts(): array
    {
        return [
            'date_mouvement' => 'date',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeParAgent($query, $agentId)
    {
        return $query->where('id_agent', $agentId);
    }

    public function scopeParService($query, $serviceId)
    {
        return $query->where('id_service', $serviceId);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_mouvement', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('date_mouvement', 'desc');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getEstAffectationInitialeAttribute()
    {
        return $this->type_mouvement === 'Affectation initiale';
    }

    public function getEstMutationAttribute()
    {
        return $this->type_mouvement === 'Mutation';
    }
}