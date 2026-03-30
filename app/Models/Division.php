<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Agent;
use App\Models\Service;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';
    protected $primaryKey = 'id_division';

    protected $fillable = [
        'nom_division',
        'id_service',
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    /**
     * Service parent de la division
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    /**
     * Agents directement affectés à cette division
     */
    public function agents()
    {
        return $this->hasMany(Agent::class, 'id_division', 'id_division');
    }

    public function getTotalAgentsCountAttribute(): int
    {
        return $this->agents()->count();
    }
}