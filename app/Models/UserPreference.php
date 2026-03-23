<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'langue',
        'theme',
        'notifications_email',
        'notifications_systeme',
        'items_par_page',
        'format_date',
    ];

    protected $casts = [
        'notifications_email'  => 'boolean',
        'notifications_systeme' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
