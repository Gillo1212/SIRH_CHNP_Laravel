<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Charger les données RH si l'utilisateur a un agent associé
        if ($user->agent) {
            $user->agent->load(['service', 'division', 'enfants', 'conjoints', 'contratActif', 'contrats']);
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's email address (seule donnée modifiable par l'utilisateur).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $user->email = $validated['email'] ?: null;
        $user->save();

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('Profil mis à jour (email)');

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('tab', 'compte');
    }

    /**
     * Redirect to preferences page.
     */
    public function settings(): RedirectResponse
    {
        return Redirect::route('preferences.index');
    }

    /**
     * Delete the user's account (Admin only / non utilisé en production CHNP).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
