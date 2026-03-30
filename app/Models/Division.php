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
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    /**
     * Services de la division (agrégation)
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'id_division', 'id_division');
    }

    /**
     * Agents affectés à cette division (via services)
     */
    public function agents()
    {
        return $this->hasManyThrough(
            Agent::class,
            Service::class,
            'id_division',  // FK sur services → divisions
            'id_service',   // FK sur agents → services
            'id_division',  // PK locale sur divisions
            'id_service'    // PK locale sur services
        );
    }

    public function getTotalAgentsCountAttribute(): int
    {
        return $this->agents()->count();
    }

    public function getServicesCountAttribute(): int
    {
        return $this->services()->count();
    }
}