<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use App\Notifications\CompteUtilisateurCreeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Formulaire de création d'un compte pour un agent sans compte.
     */
    public function createForAgent(Agent $agent)
    {
        if ($agent->user_id) {
            return back()->with('error', 'Cet agent possède déjà un compte utilisateur.');
        }

        return view('admin.users.create-for-agent', compact('agent'));
    }

    /**
     * Créer le compte utilisateur et le lier à l'agent.
     * Confidentialité CID : mot de passe temporaire, changement forcé au 1er login.
     */
    public function storeForAgent(Request $request, Agent $agent)
    {
        if ($agent->user_id) {
            return back()->with('error', 'Cet agent possède déjà un compte utilisateur.');
        }

        $validated = $request->validate([
            'login' => 'required|string|min:3|max:50|unique:users,login|regex:/^[a-z0-9._-]+$/',
            'email' => 'nullable|email|max:100|unique:users,email',
            'role'  => 'required|string|in:Agent,Manager,AgentRH,DRH,AdminSystème',
        ], [
            'login.unique'  => 'Ce login est déjà utilisé.',
            'login.regex'   => 'Le login ne peut contenir que des lettres minuscules, chiffres, points, tirets.',
            'email.unique'  => 'Cet email est déjà utilisé.',
            'role.in'       => 'Rôle invalide.',
        ]);

        $motDePasseTemporaire = Str::random(10);

        DB::transaction(function () use ($agent, $validated, $motDePasseTemporaire) {
            // Créer le compte
            $user = User::create([
                'name'                  => $agent->prenom . ' ' . $agent->nom,
                'login'                 => $validated['login'],
                'email'                 => $validated['email'] ?? null,
                'password'              => Hash::make($motDePasseTemporaire),
                'statut_compte'         => 'Actif',
                'verouille'             => false,
                'tentatives_connexion'  => 0,
                // Dossier agent déjà existant → dossier complété d'emblée
                'agent_completed'       => true,
            ]);

            // Assigner le rôle
            $user->assignRole($validated['role']);

            // Lier l'agent à son compte et marquer que le compte est créé
            $agent->update([
                'user_id'         => $user->id,
                'account_pending' => false,
            ]);

            // Envoyer les identifiants par email
            $emailDest = $validated['email'] ?? $agent->email ?? null;
            if ($emailDest) {
                $agent->notify(new CompteUtilisateurCreeNotification($user->login, $motDePasseTemporaire));
            }

            // Audit trail (Intégrité CID)
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'agent_matricule' => $agent->matricule,
                    'login'           => $user->login,
                    'role'            => $validated['role'],
                ])
                ->log("Compte utilisateur créé pour l'agent {$agent->matricule}");
        });

        return redirect()->route('admin.dashboard')
            ->with('success', "Compte créé pour {$agent->prenom} {$agent->nom}. Login : {$validated['login']}");
    }

    /**
     * Liste des agents sans compte utilisateur (en attente).
     */
    public function agentsSansCompte()
    {
        $agents = Agent::whereNull('user_id')
            ->whereNull('deleted_at')
            ->with('service')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.users.agents-sans-compte', compact('agents'));
    }
}
