<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle HeureSup
 *
 * Workflow :
 *   Major déclare → RH vérifie la conformité
 *
 * Statuts :
 *   Déclaré  → état initial, soumis par le Major
 *   Conforme → RH a vérifié : aucune altération détectée
 *   Anomalie → RH a détecté un écart, Major doit corriger
 *
 * La RH ne valide pas, elle vérifie. Elle ne supprime jamais une déclaration.
 */
class HeureSup extends Model
{
    use HasFactory;

    protected $table = 'heures_sup';
    protected $primaryKey = 'id_hsup';

    public const STATUT_DECLARE  = 'Déclaré';
    public const STATUT_CONFORME = 'Conforme';
    public const STATUT_ANOMALIE = 'Anomalie';

    protected $fillable = [
        'id_ligne',
        'nb_heures',
        'taux',
        'montant',
        'periode',
        'statut_hs',
        'note_verification',
    ];

    protected function casts(): array
    {
        return [
            'nb_heures' => 'decimal:2',
            'taux'      => 'decimal:2',
            'montant'   => 'decimal:2',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function lignePlanning()
    {
        return $this->belongsTo(LignePlanning::class, 'id_ligne', 'id_ligne');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeDeclare($query)
    {
        return $query->where('statut_hs', self::STATUT_DECLARE);
    }

    public function scopeConforme($query)
    {
        return $query->where('statut_hs', self::STATUT_CONFORME);
    }

    public function scopeAnomalie($query)
    {
        return $query->where('statut_hs', self::STATUT_ANOMALIE);
    }

    public function scopeTrimestre($query)
    {
        return $query->where('periode', 'Trimestre');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    /**
     * Dépassement attendu d'après la ligne de planning (durée réelle - 8h standard).
     */
    public function getDepassementAttenduAttribute(): float
    {
        return max(0, ($this->lignePlanning?->nb_heures ?? 0) - 8.0);
    }

    /**
     * Écart entre heures déclarées et dépassement attendu.
     * Positif = le Major a déclaré plus que prévu (surestimation).
     * Négatif = le Major a déclaré moins que prévu (sous-estimation).
     */
    public function getEcartAttribute(): float
    {
        return round($this->nb_heures - $this->depassement_attendu, 2);
    }

    /**
     * Indique si la déclaration est conforme au planning.
     */
    public function getEstConformeAttribute(): bool
    {
        return abs($this->ecart) < 0.01;
    }

    // =====================================================
    // MÉTHODES MÉTIER
    // =====================================================

    /**
     * Marquer conforme après vérification RH.
     */
    public function marquerConforme(): void
    {
        $this->update([
            'statut_hs'          => self::STATUT_CONFORME,
            'note_verification'  => null,
        ]);
    }

    /**
     * Signaler une anomalie (RH → Major doit corriger).
     */
    public function signalerAnomalie(string $note): void
    {
        $this->update([
            'statut_hs'         => self::STATUT_ANOMALIE,
            'note_verification' => $note,
        ]);
    }
}
