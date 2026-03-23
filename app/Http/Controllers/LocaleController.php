<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Changer la langue de l'application
     */
    public function switch($locale)
    {
        // Vérifier que la langue est supportée
        if (!in_array($locale, ['fr', 'en'])) {
            abort(400, 'Langue non supportée');
        }

        // Sauvegarder en session
        Session::put('locale', $locale);

        // Appliquer la langue
        App::setLocale($locale);

        // Rediriger vers la page précédente
        return redirect()->back()->with('success', __('Langue changée avec succès'));
    }
}