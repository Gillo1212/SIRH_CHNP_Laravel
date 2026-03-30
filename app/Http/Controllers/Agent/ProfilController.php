<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Aucun dossier agent associé à votre compte.');
        }

        $agent->load(['service', 'division', 'enfants', 'conjoints', 'contratActif']);

        return view('agent.profil', compact('agent'));
    }

    public function famille(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Aucun dossier agent associé à votre compte.');
        }

        $agent->load(['enfants', 'conjoints']);

        return view('agent.famille', compact('agent'));
    }

    /**
     * Mon parcours professionnel (mouvements, lecture seule)
     */
    public function monParcours(Request $request)
    {
        $agent = $request->user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Aucun dossier agent associé à votre compte.');
        }

        // Eager loading pour éviter N+1 (Disponibilité CID)
        $agent->load(['service', 'division', 'contrats']);

        $mouvements = \App\Models\Mouvement::with(['serviceDestination', 'serviceOrigine'])
            ->where('id_agent', $agent->id_agent)
            ->orderByDesc('date_mouvement')
            ->get();

        return view('agent.mon-parcours', compact('agent', 'mouvements'));
    }

    /**
     * Mon contrat (lecture seule)
     */
    public function monContrat(Request $request)
    {
        $agent = $request->user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Aucun dossier agent associé à votre compte.');
        }

        $agent->load(['service', 'division']);
        $contrats = $agent->contrats()->orderByDesc('date_debut')->get();
        $contratActif = $contrats->firstWhere('statut_contrat', 'Actif');

        return view('agent.mon-contrat', compact('agent', 'contrats', 'contratActif'));
    }

    /**
     * Upload / remplacement de la photo de profil (Disponibilité CID)
     * Sécurisé : validation stricte du type MIME + taille + nom aléatoire
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => [
                'required',
                'file',
                'image',                       // vérifie le contenu réel (GD/Imagick)
                'mimes:jpeg,png,webp',          // extensions autorisées
                'max:2048',                    // 2 Mo max
                'dimensions:min_width=80,min_height=80,max_width=1500,max_height=1500',
            ],
        ], [
            'photo.required'   => 'Veuillez sélectionner une photo.',
            'photo.image'      => 'Le fichier doit être une image valide.',
            'photo.mimes'      => 'Formats acceptés : JPEG, PNG, WebP.',
            'photo.max'        => 'La photo ne doit pas dépasser 2 Mo.',
            'photo.dimensions' => 'Les dimensions doivent être entre 80×80 et 1500×1500 px.',
        ]);

        $agent = $request->user()->agent;

        if (!$agent) {
            return back()->with('error', 'Aucun profil agent associé à votre compte.');
        }

        // Supprimer l'ancienne photo si elle existe
        if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
            Storage::disk('public')->delete($agent->photo);
        }

        // Nom de fichier aléatoire pour éviter les collisions et l'énumération (Confidentialité CID)
        $extension = $request->file('photo')->getClientOriginalExtension();
        $filename  = 'photos/' . Str::uuid() . '.' . strtolower($extension);

        // Stockage dans storage/app/public/photos/
        $request->file('photo')->storeAs('', $filename, 'public');

        $agent->update(['photo' => $filename]);

        // Audit trail — Intégrité CID
        activity()
            ->causedBy($request->user())
            ->performedOn($agent)
            ->withProperties(['action' => 'photo_update', 'ip' => $request->ip()])
            ->log('Mise à jour de la photo de profil');

        return back()->with('success', 'Photo de profil mise à jour avec succès.');
    }

    /**
     * Changement de mot de passe (self-service sécurité — Confidentialité CID)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'password.min'       => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation ne correspond pas.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mot de passe actuel incorrect.'])
                ->with('active_tab', 'securite');
        }

        $user->update(['password' => Hash::make($request->password)]);

        activity()
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip()])
            ->log('Changement de mot de passe');

        return back()
            ->with('success', 'Mot de passe modifié avec succès.')
            ->with('active_tab', 'securite');
    }
}
