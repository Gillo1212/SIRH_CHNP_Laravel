<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $preference = $user->preference ?? $user->preference()->create([
            'langue' => 'fr',
            'theme'  => 'system',
        ]);

        return view('preferences.index', compact('preference'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme'                  => 'required|in:light,dark,system',
            'notifications_email'    => 'nullable|boolean',
            'notifications_systeme'  => 'nullable|boolean',
            'items_par_page'         => 'required|integer|in:10,15,25,50',
            'format_date'            => 'required|in:d/m/Y,Y-m-d,m/d/Y',
        ]);

        // Les checkboxes non cochées ne sont pas envoyées
        $validated['notifications_email']   = $request->boolean('notifications_email');
        $validated['notifications_systeme'] = $request->boolean('notifications_systeme');

        $user = Auth::user();
        $pref = $user->preference ?? $user->preference()->create([]);
        $pref->update($validated);

        session(['theme' => $validated['theme']]);

        return back()->with('success', __('messages.preferences_saved'));
    }

    public function updateTheme(Request $request)
    {
        $theme = $request->input('theme');
        if (!in_array($theme, ['light', 'dark', 'system'])) {
            return response()->json(['error' => 'Invalid theme'], 422);
        }

        session(['theme' => $theme]);

        if (Auth::check()) {
            try {
                $pref = Auth::user()->preference;
                if ($pref) $pref->update(['theme' => $theme]);
            } catch (\Exception $e) {
                // silently fail
            }
        }

        return response()->json(['success' => true]);
    }

}
