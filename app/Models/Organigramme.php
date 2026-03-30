<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organigramme extends Model
{
    protected $table = 'organigramme';

    protected $fillable = [
        'titre',
        'donnees_json',
        'cree_par',
    ];

    protected $casts = [
        'donnees_json' => 'array',
    ];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }
}
