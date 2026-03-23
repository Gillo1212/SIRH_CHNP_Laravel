<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogAudit extends Model
{
    use HasFactory;

    protected $table = 'log_audit';
    protected $primaryKey = 'id_log';

    public $timestamps = false;
    const CREATED_AT = 'date_evenement';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_utilisateur',
        'action',
        'table_cible',
        'details',
        'adresse_ip',
        'date_evenement',
    ];

    protected function casts(): array
    {
        return [
            'date_evenement' => 'datetime',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeParAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeParTable($query, $table)
    {
        return $query->where('table_cible', $table);
    }

    public function scopeParUtilisateur($query, $userId)
    {
        return $query->where('id_utilisateur', $userId);
    }

    public function scopeRecent($query, $jours = 30)
    {
        return $query->where('date_evenement', '>=', now()->subDays($jours));
    }

    // =====================================================
    // MÉTHODES STATIQUES
    // =====================================================

    /**
     * Logger une action
     */
    public static function logAction($action, $table, $details = null)
    {
        return self::create([
            'id_utilisateur' => Auth::id(),
            'action' => $action,
            'table_cible' => $table,
            'details' => $details,
            'adresse_ip' => request()->ip(),
            'date_evenement' => now(),
        ]);
    }
}
