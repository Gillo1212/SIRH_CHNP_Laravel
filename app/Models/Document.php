<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $primaryKey = 'id_document';

    protected $fillable = [
        'id_dossier',
        'reference',
        'titre',
        'mots_cles',
        'date_creation',
        'date_archivage',
        'document_url',
        'type_document',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'date',
            'date_archivage' => 'datetime',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function dossier()
    {
        return $this->belongsTo(DossierAgent::class, 'id_dossier', 'id_dossier');
    }

    /**
     * Accès direct à l'agent via le dossier
     */
    public function agent()
    {
        return $this->hasOneThrough(
            User::class,
            DossierAgent::class,
            'id_dossier',
            'id',
            'id_dossier',
            'id_agent'
        );
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeParType($query, $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopeRecherche($query, $terme)
    {
        return $query->where(function ($q) use ($terme) {
            $q->where('titre', 'like', "%{$terme}%")
              ->orWhere('mots_cles', 'like', "%{$terme}%")
              ->orWhere('reference', 'like', "%{$terme}%");
        });
    }

    public function scopeRecent($query, $jours = 30)
    {
        return $query->where('date_archivage', '>=', now()->subDays($jours));
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNomFichierAttribute()
    {
        return basename($this->document_url);
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->document_url, PATHINFO_EXTENSION);
    }

    public function getTailleAttribute()
    {
        $chemin = storage_path('app/public/' . $this->document_url);
        return file_exists($chemin) ? filesize($chemin) : 0;
    }

    public function getTailleHumainAttribute()
    {
        $taille = $this->taille;
        $unites = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        
        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }
        
        return round($taille, 2) . ' ' . $unites[$i];
    }

    public function getEstPdfAttribute()
    {
        return strtolower($this->extension) === 'pdf';
    }

    public function getEstImageAttribute()
    {
        return in_array(strtolower($this->extension), ['jpg', 'jpeg', 'png', 'gif']);
    }

    public function getEstContratAttribute()
    {
        return $this->type_document === 'Contrat';
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Générer une référence unique
     */
    public static function genererReference($typeDocument)
    {
        $prefixes = [
            'Contrat' => 'CONT',
            'Attestation' => 'ATT',
            'Décision' => 'DEC',
            'Ordre_mission' => 'OM',
            'Nomination' => 'NOM',
            'PV' => 'PV',
            'Domiciliation' => 'DOM',
        ];
        
        $prefix = $prefixes[$typeDocument] ?? 'DOC';
        $annee = date('Y');
        
        $dernierDoc = self::where('reference', 'like', "{$prefix}-{$annee}-%")
                          ->orderBy('id_document', 'desc')
                          ->first();
        
        if ($dernierDoc) {
            $numero = (int) substr($dernierDoc->reference, -4) + 1;
        } else {
            $numero = 1;
        }
        
        return "{$prefix}-{$annee}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Télécharger le document
     */
    public function telecharger()
    {
        return response()->download(storage_path('app/public/' . $this->document_url));
    }
}