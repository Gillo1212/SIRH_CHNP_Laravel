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
        'description',
        'notes',
        'date_archivage',
        'date_cloture',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
            'date_archivage' => 'datetime',
            'date_cloture'  => 'datetime',
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

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_dossier', 'id_dossier');
    }

    public function documentsActifs()
    {
        return $this->hasMany(Document::class, 'id_dossier', 'id_dossier')
                    ->where('statut_document', 'Actif');
    }

    public function documentsArchives()
    {
        return $this->hasMany(Document::class, 'id_dossier', 'id_dossier')
                    ->where('statut_document', 'Archivé');
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

    public function getNombreDocumentsAttribute(): int
    {
        if ($this->relationLoaded('documents')) {
            return $this->documents->count();
        }
        return $this->documents()->count();
    }

    public function getNombreDocumentsActifsAttribute(): int
    {
        if ($this->relationLoaded('documents')) {
            return $this->documents->where('statut_document', 'Actif')->count();
        }
        return $this->documents()->where('statut_document', 'Actif')->count();
    }

    public function getEstActifAttribute(): bool
    {
        return $this->statut_da === 'Actif';
    }

    public function getStatutBadgeAttribute(): string
    {
        return match ($this->statut_da) {
            'Actif'    => 'bg-success',
            'Archivé'  => 'bg-secondary',
            'Clôturé'  => 'bg-danger',
            default    => 'bg-secondary',
        };
    }

    public function getTauxRemplissageAttribute(): int
    {
        // Nombre de types de documents couverts / total des types principaux
        $typesPrincipaux = ['Contrat', 'Piece_identite', 'Diplome'];
        $typesPresents = $this->documents()
                              ->whereIn('type_document', $typesPrincipaux)
                              ->where('statut_document', 'Actif')
                              ->distinct('type_document')
                              ->count('type_document');
        return (int) round(($typesPresents / count($typesPrincipaux)) * 100);
    }

    // =====================================================
    // MÉTHODES CYCLE DE VIE
    // =====================================================

    public function archiver(): void
    {
        $this->update([
            'statut_da'      => 'Archivé',
            'date_archivage' => now(),
        ]);
        // Archiver aussi tous les documents actifs
        $this->documentsActifs()->update([
            'statut_document' => 'Archivé',
            'date_archivage'  => now(),
        ]);
    }

    public function cloturer(): void
    {
        $this->update([
            'statut_da'    => 'Clôturé',
            'date_cloture' => now(),
        ]);
    }

    public function reactiver(): void
    {
        $this->update([
            'statut_da'      => 'Actif',
            'date_archivage' => null,
            'date_cloture'   => null,
        ]);
    }

    // =====================================================
    // GÉNÉRATION RÉFÉRENCE
    // =====================================================

    public static function genererReference(): string
    {
        $annee = date('Y');
        $dernierDossier = self::where('reference', 'like', "DOSS-{$annee}-%")
                              ->orderBy('id_dossier', 'desc')
                              ->first();
        $numero = $dernierDossier ? ((int) substr($dernierDossier->reference, -4) + 1) : 1;
        return "DOSS-{$annee}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
