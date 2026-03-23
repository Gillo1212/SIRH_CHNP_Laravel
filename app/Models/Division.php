<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Agents affectés à cette division
     */
    public function agents()
    {
        return $this->hasMany(User::class, 'id_division', 'id_division');
    }
}