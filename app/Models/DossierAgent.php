<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierAgent extends Model
{
    use HasFactory;

    protected $table = 'dossier_agents';
    protected $primaryKey = 'id_dossier';

    protected $fillable = [
        'id_etagere',
        'id_agent',
        'reference',
        'date_creation',
        'statut_da',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function etagere()
    {
        return $this->belongsTo(Etagere::class, 'id_etagere', 'id_etagere');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    /**
     * Documents contenus (composition)
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_dossier', 'id_dossier');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActif($query)
    {
        return $query->where('statut_da', 'Actif');
    }

    public function scopeArchive($query)
    {
        return $query->where('statut_da', 'Archivé');
    }

    public function scopeCloture($query)
    {
        return $query->where('statut_da', 'Clôturé');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNombreDocumentsAttribute()
    {
        return $this->documents()->count();
    }

    public function getEstActifAttribute()
    {
        return $this->statut_da === 'Actif';
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Archiver le dossier
     */
    public function archiver()
    {
        $this->update(['statut_da' => 'Archivé']);
    }

    /**
     * Clôturer le dossier
     */
    public function cloturer()
    {
        $this->update(['statut_da' => 'Clôturé']);
    }

    /**
     * Réactiver le dossier
     */
    public function reactiver()
    {
        $this->update(['statut_da' => 'Actif']);
    }

    /**
     * Générer une référence unique
     */
    public static function genererReference()
    {
        $annee = date('Y');
        $dernierDossier = self::where('reference', 'like', "DOSS-{$annee}-%")
                              ->orderBy('id_dossier', 'desc')
                              ->first();
        
        if ($dernierDossier) {
            $numero = (int) substr($dernierDossier->reference, -4) + 1;
        } else {
            $numero = 1;
        }
        
        return "DOSS-{$annee}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}