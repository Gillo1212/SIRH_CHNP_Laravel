<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Modèle pour les demandes de documents administratifs.
 * 
 * @property int $id
 * @property int $agent_id
 * @property string $type_document
 * @property string|null $motif
 * @property array|null $donnees_specifiques
 * @property string $statut
 * @property int|null $traite_par
 * @property \Carbon\Carbon|null $date_traitement
 * @property string|null $fichier_genere
 * @property string|null $numero_reference
 * @property \Carbon\Carbon|null $date_debut_validite
 * @property \Carbon\Carbon|null $date_fin_validite
 * @property int|null $agent_remplacant_id
 * @property int|null $service_destination_id
 * @property string|null $motif_rejet
 * 
 * @author Gilbert - Mémoire M2 SIRH CHNP
 */
class DemandeDocument extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'demandes_documents';

    /**
     * Types de documents disponibles.
     */
    public const TYPES_DOCUMENTS = [
        'attestation_travail'             => 'Attestation de travail',
        'certificat_travail'              => 'Certificat de travail',
        'decision_conge_administratif'    => 'Décision de congé administratif',
        'attestation_jouissance_conge'    => 'Attestation de jouissance de congé',
        'attestation_cessation_maternite' => 'Attestation de cessation (maternité)',
        'note_affectation'                => 'Note d\'affectation',
        'note_interim'                    => 'Note de service d\'intérim',
        'ordre_mission'                   => 'Ordre de mission',
        'autorisation_sortie_territoire'  => 'Autorisation de sortie du territoire',
        'attestation_prime_motivation'    => 'Attestation de prime de motivation',
        'attestation_prise_service'       => 'Attestation de prise de service',
        'attestation_stage'               => 'Attestation de stage',
    ];

    /**
     * Catégories de documents.
     */
    public const CATEGORIES_DOCUMENTS = [
        'attestations' => [
            'label' => 'Attestations',
            'icon'  => 'fas fa-file-alt',
            'types' => ['attestation_travail', 'certificat_travail'],
        ],
        'conges' => [
            'label' => 'Congés et absences',
            'icon'  => 'fas fa-calendar-check',
            'types' => ['decision_conge_administratif', 'attestation_jouissance_conge', 'attestation_cessation_maternite'],
        ],
        'mouvements' => [
            'label' => 'Mouvements de personnel',
            'icon'  => 'fas fa-exchange-alt',
            'types' => ['note_affectation', 'note_interim'],
        ],
        'missions' => [
            'label' => 'Missions et déplacements',
            'icon'  => 'fas fa-plane',
            'types' => ['ordre_mission', 'autorisation_sortie_territoire'],
        ],
        'autres' => [
            'label' => 'Autres attestations',
            'icon'  => 'fas fa-file-signature',
            'types' => ['attestation_prime_motivation', 'attestation_prise_service', 'attestation_stage'],
        ],
    ];

    /**
     * Statuts possibles.
     */
    public const STATUTS = [
        'en_attente' => ['label' => 'En attente', 'color' => 'warning', 'icon' => 'fas fa-clock'],
        'en_cours'   => ['label' => 'En cours', 'color' => 'info', 'icon' => 'fas fa-spinner'],
        'pret'       => ['label' => 'Prêt', 'color' => 'success', 'icon' => 'fas fa-check-circle'],
        'rejete'     => ['label' => 'Rejeté', 'color' => 'danger', 'icon' => 'fas fa-times-circle'],
    ];

    protected $fillable = [
        'agent_id',
        'type_document',
        'motif',
        'donnees_specifiques',
        'statut',
        'traite_par',
        'date_traitement',
        'fichier_genere',
        'numero_reference',
        'date_debut_validite',
        'date_fin_validite',
        'agent_remplacant_id',
        'service_destination_id',
        'motif_rejet',
    ];

    protected function casts(): array
    {
        return [
            'donnees_specifiques' => 'array',
            'date_traitement'     => 'datetime',
            'date_debut_validite' => 'date',
            'date_fin_validite'   => 'date',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id_agent');
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par', 'id');
    }

    public function agentRemplacant()
    {
        return $this->belongsTo(Agent::class, 'agent_remplacant_id', 'id_agent');
    }

    public function serviceDestination()
    {
        return $this->belongsTo(Service::class, 'service_destination_id', 'id_service');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getLibelleTypeAttribute(): string
    {
        return self::TYPES_DOCUMENTS[$this->type_document] ?? ucfirst(str_replace('_', ' ', $this->type_document));
    }

    public function getLibelleStatutAttribute(): string
    {
        return self::STATUTS[$this->statut]['label'] ?? ucfirst($this->statut);
    }

    public function getCouleurStatutAttribute(): string
    {
        return self::STATUTS[$this->statut]['color'] ?? 'secondary';
    }

    public function getIconeStatutAttribute(): string
    {
        return self::STATUTS[$this->statut]['icon'] ?? 'fas fa-question';
    }

    public function getCategorieAttribute(): ?string
    {
        foreach (self::CATEGORIES_DOCUMENTS as $key => $category) {
            if (in_array($this->type_document, $category['types'])) {
                return $key;
            }
        }
        return null;
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePret($query)
    {
        return $query->where('statut', 'pret');
    }

    public function scopeATraiter($query)
    {
        return $query->whereIn('statut', ['en_attente', 'en_cours']);
    }

    public function scopeDuType($query, string $type)
    {
        return $query->where('type_document', $type);
    }

    // =====================================================
    // MÉTHODES MÉTIER
    // =====================================================

    /**
     * Génère le numéro de référence officiel.
     */
    public function genererNumeroReference(): string
    {
        $prefixes = [
            'attestation_travail'             => 'ATT',
            'certificat_travail'              => 'CER',
            'decision_conge_administratif'    => 'DCA',
            'attestation_jouissance_conge'    => 'AJC',
            'attestation_cessation_maternite' => 'ACM',
            'note_affectation'                => 'NAF',
            'note_interim'                    => 'NIT',
            'ordre_mission'                   => 'ORM',
            'autorisation_sortie_territoire'  => 'AST',
            'attestation_prime_motivation'    => 'APM',
            'attestation_prise_service'       => 'APS',
            'attestation_stage'               => 'STG',
        ];

        $prefix = $prefixes[$this->type_document] ?? 'DOC';
        $annee = now()->year;
        
        $count = self::where('type_document', $this->type_document)
            ->whereYear('created_at', $annee)
            ->whereNotNull('numero_reference')
            ->count() + 1;

        return sprintf('MSHP/CHNP/DIR/SRH/%s/%d/%05d', $prefix, $annee, $count);
    }

    /**
     * Récupère une donnée spécifique.
     */
    public function getDonnee(string $cle, $defaut = null)
    {
        return data_get($this->donnees_specifiques, $cle, $defaut);
    }

    // =====================================================
    // ACTIVITY LOG
    // =====================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type_document', 'statut', 'traite_par', 'numero_reference'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('demandes_documents');
    }
}
