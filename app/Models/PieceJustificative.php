<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PieceJustificative extends Model
{
    use HasFactory;

    protected $table = 'pieces_justificatives';
    protected $primaryKey = 'id_piece';

    protected $fillable = [
        'id_absence',
        'type_piece',
        'fichier_url',
        'date_depot',
        'valide',
    ];

    protected function casts(): array
    {
        return [
            'date_depot' => 'datetime',
            'valide' => 'boolean',
        ];
    }

    // =====================================================
    // RELATIONS
    // =====================================================

    public function absence()
    {
        return $this->belongsTo(Absence::class, 'id_absence', 'id_absence');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeValide($query)
    {
        return $query->where('valide', true);
    }

    public function scopeNonValide($query)
    {
        return $query->where('valide', false);
    }

    public function scopeCertificatMedical($query)
    {
        return $query->where('type_piece', 'Certificat médical');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getNomFichierAttribute()
    {
        return basename($this->fichier_url);
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->fichier_url, PATHINFO_EXTENSION);
    }

    public function getEstPdfAttribute()
    {
        return strtolower($this->extension) === 'pdf';
    }

    public function getEstImageAttribute()
    {
        return in_array(strtolower($this->extension), ['jpg', 'jpeg', 'png', 'gif']);
    }

    // =====================================================
    // MÉTHODES
    // =====================================================

    /**
     * Valider la pièce justificative
     */
    public function valider()
    {
        $this->update(['valide' => true]);
        
        // Mettre à jour l'absence associée
        $this->absence->update(['justifie' => true]);
    }

    /**
     * Rejeter la pièce justificative
     */
    public function rejeter()
    {
        $this->update(['valide' => false]);
    }
}