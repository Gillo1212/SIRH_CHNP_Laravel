<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Document extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'documents';
    protected $primaryKey = 'id_document';

    protected $fillable = [
        'id_dossier',
        'reference',
        'titre',
        'mots_cles',
        'description',
        'date_creation',
        'date_archivage',
        'document_url',
        'type_document',
        'statut_document',
        'niveau_confidentialite',
        'format_fichier',
        'taille_fichier',
        'version',
        'charge_par',
        'date_destruction',
    ];

    protected function casts(): array
    {
        return [
            'date_creation'    => 'date',
            'date_archivage'   => 'datetime',
            'date_destruction' => 'datetime',
        ];
    }

    // =====================================================
    // ACTIVITY LOG (Intégrité CID)
    // =====================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titre', 'type_document', 'statut_document', 'niveau_confidentialite', 'version'])
            ->logOnlyDirty()
            ->useLogName('documents');
    }

    // =====================================================
    // CONSTANTES
    // =====================================================

    const TYPES = [
        'Contrat'            => ['label' => 'Contrat',                   'icon' => 'ri-file-text-line',    'color' => '#1D4ED8'],
        'Attestation'        => ['label' => 'Attestation',               'icon' => 'ri-award-line',        'color' => '#059669'],
        'Décision'           => ['label' => 'Décision',                  'icon' => 'ri-government-line',   'color' => '#DC2626'],
        'Ordre_mission'      => ['label' => "Ordre de mission",          'icon' => 'ri-road-map-line',     'color' => '#D97706'],
        'Nomination'         => ['label' => 'Nomination',                'icon' => 'ri-medal-line',        'color' => '#7C3AED'],
        'PV'                 => ['label' => "Procès-verbal",             'icon' => 'ri-article-line',      'color' => '#0891B2'],
        'Domiciliation'      => ['label' => 'Domiciliation',             'icon' => 'ri-home-3-line',       'color' => '#6B7280'],
        'Diplome'            => ['label' => 'Diplôme',                   'icon' => 'ri-graduation-cap-line','color'=> '#B45309'],
        'Certificat_medical' => ['label' => 'Certificat médical',        'icon' => 'ri-heart-pulse-line',  'color' => '#BE123C'],
        'Fiche_evaluation'   => ['label' => "Fiche d'évaluation",        'icon' => 'ri-survey-line',       'color' => '#0E7490'],
        'Piece_identite'     => ['label' => "Pièce d'identité",          'icon' => 'ri-id-card-line',      'color' => '#374151'],
        'Autre'              => ['label' => 'Autre document',            'icon' => 'ri-file-line',         'color' => '#6B7280'],
    ];

    const NIVEAUX_CONFIDENTIALITE = [
        'Public'       => ['label' => 'Public',       'badge' => 'bg-success', 'icon' => 'ri-global-line'],
        'Interne'      => ['label' => 'Interne',      'badge' => 'bg-primary', 'icon' => 'ri-building-line'],
        'Confidentiel' => ['label' => 'Confidentiel', 'badge' => 'bg-warning', 'icon' => 'ri-lock-line'],
        'Secret'       => ['label' => 'Secret',       'badge' => 'bg-danger',  'icon' => 'ri-spy-line'],
    ];

    const STATUTS = [
        'Actif'   => ['label' => 'Actif',   'badge' => 'bg-success', 'icon' => 'ri-checkbox-circle-line'],
        'Archivé' => ['label' => 'Archivé', 'badge' => 'bg-secondary','icon' => 'ri-archive-line'],
        'Détruit' => ['label' => 'Détruit', 'badge' => 'bg-danger',  'icon' => 'ri-delete-bin-line'],
    ];

    // =====================================================
    // RELATIONS
    // =====================================================

    public function dossier()
    {
        return $this->belongsTo(DossierAgent::class, 'id_dossier', 'id_dossier');
    }

    public function uploadePar()
    {
        return $this->belongsTo(User::class, 'charge_par');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeParType($query, $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopeActif($query)
    {
        return $query->where('statut_document', 'Actif');
    }

    public function scopeArchive($query)
    {
        return $query->where('statut_document', 'Archivé');
    }

    public function scopeConfidentiels($query)
    {
        return $query->whereIn('niveau_confidentialite', ['Confidentiel', 'Secret']);
    }

    public function scopeVisiblePour($query, string $role)
    {
        $niveaux = match ($role) {
            'AdminSystème', 'DRH' => ['Public', 'Interne', 'Confidentiel', 'Secret'],
            'AgentRH'             => ['Public', 'Interne', 'Confidentiel'],
            'Manager'             => ['Public', 'Interne'],
            default               => ['Public'],         // Agent voit ses propres docs publics
        };
        return $query->whereIn('niveau_confidentialite', $niveaux);
    }

    public function scopeRecherche($query, $terme)
    {
        return $query->where(function ($q) use ($terme) {
            $q->where('titre', 'like', "%{$terme}%")
              ->orWhere('mots_cles', 'like', "%{$terme}%")
              ->orWhere('reference', 'like', "%{$terme}%")
              ->orWhere('description', 'like', "%{$terme}%");
        });
    }

    public function scopeRecent($query, $jours = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($jours));
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNomFichierAttribute(): string
    {
        return basename($this->document_url);
    }

    public function getExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->document_url, PATHINFO_EXTENSION));
    }

    public function getTailleAttribute(): int
    {
        $chemin = storage_path('app/public/' . $this->document_url);
        return file_exists($chemin) ? filesize($chemin) : 0;
    }

    public function getTailleHumainAttribute(): string
    {
        $taille = $this->taille_fichier ?? $this->taille;
        $unites = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0;
        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }
        return round($taille, 2) . ' ' . $unites[$i];
    }

    public function getEstPdfAttribute(): bool
    {
        return in_array($this->extension, ['pdf']);
    }

    public function getEstImageAttribute(): bool
    {
        return in_array($this->extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function getEstActifAttribute(): bool
    {
        return $this->statut_document === 'Actif';
    }

    public function getTypeInfoAttribute(): array
    {
        return self::TYPES[$this->type_document] ?? ['label' => $this->type_document, 'icon' => 'ri-file-line', 'color' => '#6B7280'];
    }

    public function getConfidentialiteInfoAttribute(): array
    {
        return self::NIVEAUX_CONFIDENTIALITE[$this->niveau_confidentialite] ?? ['label' => $this->niveau_confidentialite, 'badge' => 'bg-secondary', 'icon' => 'ri-lock-line'];
    }

    public function getCheminAbsoluAttribute(): string
    {
        return storage_path('app/public/' . $this->document_url);
    }

    public function getCheminPublicAttribute(): string
    {
        return asset('storage/' . $this->document_url);
    }

    // =====================================================
    // MÉTHODES CYCLE DE VIE
    // =====================================================

    public function archiver(?int $parUserId = null): void
    {
        $this->update([
            'statut_document' => 'Archivé',
            'date_archivage'  => now(),
        ]);
        activity('documents')
            ->causedBy($parUserId ? User::find($parUserId) : auth()->user())
            ->performedOn($this)
            ->withProperties(['action' => 'archivage'])
            ->log("Document archivé : {$this->titre}");
    }

    public function restaurer(): void
    {
        $this->update(['statut_document' => 'Actif']);
        activity('documents')
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties(['action' => 'restauration'])
            ->log("Document restauré : {$this->titre}");
    }

    public function detruire(): void
    {
        $this->update([
            'statut_document'  => 'Détruit',
            'date_destruction' => now(),
        ]);
        activity('documents')
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties(['action' => 'destruction'])
            ->log("Document détruit : {$this->titre}");
    }

    public function nouvelleVersion(): string
    {
        [$major, $minor] = explode('.', $this->version ?? '1.0');
        return $major . '.' . ($minor + 1);
    }

    // =====================================================
    // GÉNÉRATION RÉFÉRENCE
    // =====================================================

    public static function genererReference(string $typeDocument): string
    {
        $prefixes = [
            'Contrat'            => 'CONT',
            'Attestation'        => 'ATT',
            'Décision'           => 'DEC',
            'Ordre_mission'      => 'OM',
            'Nomination'         => 'NOM',
            'PV'                 => 'PV',
            'Domiciliation'      => 'DOM',
            'Diplome'            => 'DIP',
            'Certificat_medical' => 'CM',
            'Fiche_evaluation'   => 'EVAL',
            'Piece_identite'     => 'PI',
            'Autre'              => 'DOC',
        ];

        $prefix = $prefixes[$typeDocument] ?? 'DOC';
        $annee  = date('Y');

        $dernierDoc = self::where('reference', 'like', "{$prefix}-{$annee}-%")
                         ->orderBy('id_document', 'desc')
                         ->first();

        $numero = $dernierDoc ? ((int) substr($dernierDoc->reference, -4) + 1) : 1;

        return "{$prefix}-{$annee}-" . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    // =====================================================
    // VÉRIFICATION ACCÈS (Confidentialité CID)
    // =====================================================

    public function estVisiblePar(User $user): bool
    {
        if ($user->hasRole(['AdminSystème', 'DRH'])) {
            return true;
        }
        if ($user->hasRole('AgentRH')) {
            return in_array($this->niveau_confidentialite, ['Public', 'Interne', 'Confidentiel']);
        }
        if ($user->hasRole('Manager')) {
            return in_array($this->niveau_confidentialite, ['Public', 'Interne']);
        }
        // Agent : ne voit que ses propres documents publics/internes
        $agent = $user->agent;
        if ($agent && $agent->dossier && $agent->dossier->id_dossier === $this->id_dossier) {
            return in_array($this->niveau_confidentialite, ['Public', 'Interne']);
        }
        return false;
    }
}
