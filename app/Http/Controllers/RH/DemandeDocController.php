<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\DemandeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeDocController extends Controller
{
    public function index()
    {
        $demandes = DemandeDocument::with('agent')
            ->latest()
            ->paginate(20);

        $stats = [
            'total'     => DemandeDocument::count(),
            'en_attente'=> DemandeDocument::where('statut', 'en_attente')->count(),
            'en_cours'  => DemandeDocument::where('statut', 'en_cours')->count(),
            'pret'      => DemandeDocument::where('statut', 'pret')->count(),
            'rejete'    => DemandeDocument::where('statut', 'rejete')->count(),
        ];

        return view('rh.demandes-docs.index', compact('demandes', 'stats'));
    }

    public function pending()
    {
        $demandes = DemandeDocument::with('agent')
            ->whereIn('statut', ['en_attente', 'en_cours'])
            ->latest()
            ->paginate(20);

        return view('rh.demandes-docs.pending', compact('demandes'));
    }

    public function show($id)
    {
        $demande = DemandeDocument::with(['agent.service', 'traitePar'])->findOrFail($id);
        return view('rh.demandes-docs.show', compact('demande'));
    }

    public function traiter(Request $request, $id)
    {
        $demande = DemandeDocument::with('agent')->findOrFail($id);

        if (!in_array($demande->statut, ['en_attente', 'en_cours'])) {
            return back()->with('error', 'Cette demande ne peut pas être traitée dans son état actuel.');
        }

        $demande->update([
            'statut'          => 'pret',
            'traite_par'      => Auth::id(),
            'date_traitement' => now(),
        ]);

        activity()->causedBy(Auth::user())->on($demande)->log('Demande document traitée : ' . $demande->libelleType);

        return back()->with('success', 'Demande traitée. Le document est prêt pour l\'agent.');
    }

    public function rejeter(Request $request, $id)
    {
        $request->validate(['motif_rejet' => 'nullable|string|max:500']);

        $demande = DemandeDocument::findOrFail($id);

        if (!in_array($demande->statut, ['en_attente', 'en_cours'])) {
            return back()->with('error', 'Cette demande ne peut pas être rejetée dans son état actuel.');
        }

        $demande->update([
            'statut'          => 'rejete',
            'motif_rejet'     => $request->input('motif_rejet', 'Demande rejetée par RH'),
            'traite_par'      => Auth::id(),
            'date_traitement' => now(),
        ]);

        activity()->causedBy(Auth::user())->on($demande)->log('Demande document rejetée');

        return back()->with('success', 'Demande rejetée.');
    }
}
