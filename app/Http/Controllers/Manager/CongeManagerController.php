<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CongeManagerController extends Controller
{
    /**
     * Congés en attente de validation Manager (équipe du manager)
     */
    public function pending()
    {
        $manager = Auth::user()->agent;

        if (!$manager || !$manager->id_service) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Votre profil manager n\'est pas correctement configuré.');
        }

        // Récupérer les demandes de congé des agents du même service
        $pending = Demande::with(['conge.typeConge', 'agent.service'])
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->whereHas('agent', function ($q) use ($manager) {
                $q->where('id_service', $manager->id_service)
                  ->where('id_agent', '!=', $manager->id_agent);
            })
            ->orderByDesc('created_at')
            ->get();

        // Déjà traitées récemment (7 derniers jours)
        $traitees = Demande::with(['conge.typeConge', 'agent'])
            ->where('type_demande', 'Conge')
            ->whereIn('statut_demande', ['Validé', 'Rejeté'])
            ->whereHas('agent', function ($q) use ($manager) {
                $q->where('id_service', $manager->id_service)
                  ->where('id_agent', '!=', $manager->id_agent);
            })
            ->where('date_traitement', '>=', now()->subDays(7))
            ->orderByDesc('date_traitement')
            ->get();

        return view('manager.conges.pending', compact('pending', 'traitees'));
    }

    /**
     * Valider un congé (1ère étape du workflow)
     */
    public function valider($id)
    {
        $manager = Auth::user()->agent;

        $demande = Demande::with(['conge', 'agent'])
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->whereHas('agent', function ($q) use ($manager) {
                $q->where('id_service', $manager->id_service);
            })
            ->findOrFail($id);

        $demande->update([
            'statut_demande'  => 'Validé',
            'date_traitement' => now(),
        ]);

        $nomAgent = $demande->agent->nom_complet ?? 'l\'agent';
        $nbJours  = $demande->conge->nbres_jours ?? '';

        return back()->with('success', "La demande de congé de {$nomAgent} ({$nbJours} jour(s)) a été validée et transmise au service RH.");
    }

    /**
     * Rejeter un congé
     */
    public function rejeter(Request $request, $id)
    {
        $manager = Auth::user()->agent;

        $request->validate([
            'motif_refus' => 'required|string|min:10|max:500',
        ], [
            'motif_refus.required' => 'Veuillez indiquer le motif du rejet.',
            'motif_refus.min'      => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $demande = Demande::with(['conge', 'agent'])
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->whereHas('agent', function ($q) use ($manager) {
                $q->where('id_service', $manager->id_service);
            })
            ->findOrFail($id);

        $demande->update([
            'statut_demande'  => 'Rejeté',
            'motif_refus'     => $request->motif_refus,
            'date_traitement' => now(),
        ]);

        $nomAgent = $demande->agent->nom_complet ?? 'l\'agent';

        return back()->with('success', "La demande de congé de {$nomAgent} a été rejetée.");
    }
}
