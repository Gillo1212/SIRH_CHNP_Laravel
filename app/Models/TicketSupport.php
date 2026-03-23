<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSupport extends Model
{
    protected $table = 'tickets_support';

    protected $fillable = [
        'user_id',
        'sujet',
        'categorie',
        'priorite',
        'description',
        'capture_ecran',
        'statut',
        'reponse',
        'traite_par',
        'date_resolution',
    ];

    protected $casts = [
        'date_resolution' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function traitePar()
    {
        return $this->belongsTo(User::class, 'traite_par');
    }
}
