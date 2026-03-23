<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserAccountRequest;
use App\Http\Requests\Admin\UpdateUserAccountRequest;
use App\Models\Agent;
use App\Models\User;
use App\Notifications\NouveauCompteARHNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserAccountController extends Controller
{
    // =========================================================
    // LISTE — Tous les comptes
    // =========================================================

    public function index(Request $request)
    {
        $query = User::with(['roles', 'agent']);

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('dossier_status')) {
            $query->where('agent_completed', $request->dossier_status === 'completed');
        }

        if ($request->filled('statut')) {
            $query->where('statut_compte', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('login', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        $stats = [
            'total'       => User::count(),
            'completes'   => User::where('agent_completed', true)->count(),
            'attente'     => User::where('agent_completed', false)->count(),
            'verrouilles' => User::where('verouille', true)->count(),
        ];

        return view('admin.accounts.index', compact('users', 'roles', 'stats'));
    }

    // =========================================================
    // PAGE COMBINÉE — Agents sans compte
    // =========================================================

    public function agentsSansCompte()
    {
        // Agents créés par la RH sans compte utilisateur (en attente de création de compte)
        $agentsSansUser = Agent::whereNull('user_id')
            ->where('account_pending', true)
            ->whereNull('deleted_at')
            ->with('service')
            ->latest()
            ->get();

        // Comptes créés par l'Admin en attente de dossier RH
        $comptesSansDossier = User::where('agent_completed', false)
            ->with('roles')
            ->latest()
            ->get();

        return view('admin.accounts.agents-sans-compte', compact('agentsSansUser', 'comptesSansDossier'));
    }

    // =========================================================
    // ENDPOINT JSON — Données d'un compte pour le modal édition
    // =========================================================

    public function getData(int $id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'id'    => $user->id,
            'login' => $user->login,
            'email' => $user->email ?? '',
            'roles' => $user->roles->pluck('name')->toArray(),
        ]);
    }

    // =========================================================
    // CRÉER (modal → store → redirect index)
    // =========================================================

    public function store(StoreUserAccountRequest $request)
    {
        try {
            $user = User::create([
                'login'                => $request->login,
                'email'                => $request->email,
                'password'             => Hash::make($request->password),
                'statut_compte'        => 'Actif',
                'verouille'            => false,
                'tentatives_connexion' => 0,
                'agent_completed'      => false,
            ]);

            $user->syncRoles($request->roles);

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['roles' => $request->roles, 'email' => $request->email])
                ->log('Compte utilisateur créé par l\'administrateur');

            if ($request->boolean('notify_rh')) {
                $this->notifyRH($user);
            }

            $msg = "Compte <strong>{$user->login}</strong> créé avec succès."
                 . ($request->boolean('notify_rh') ? ' La RH a été notifiée.' : '');

            return redirect()->route('admin.accounts.index')->with('success', $msg);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    // =========================================================
    // AFFICHER — Détail compte
    // =========================================================

    public function show(int $id)
    {
        $user = User::with(['roles', 'agent.service'])->findOrFail($id);

        $auditLogs = Activity::forSubject($user)
            ->latest()
            ->take(20)
            ->get();

        return view('admin.accounts.show', compact('user', 'auditLogs'));
    }

    // =========================================================
    // ÉDITER (modal → update → redirect index)
    // =========================================================

    public function update(UpdateUserAccountRequest $request, int $id)
    {
        $user = User::findOrFail($id);

        try {
            $user->update([
                'login' => $request->login,
                'email' => $request->email,
            ]);

            $user->syncRoles($request->roles);

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['roles' => $request->roles])
                ->log('Compte utilisateur modifié');

            $redirect = url()->previous() === route('admin.accounts.index')
                ? route('admin.accounts.index')
                : route('admin.accounts.show', $user->id);

            return redirect($redirect)
                ->with('success', "Compte <strong>{$user->login}</strong> mis à jour.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    // =========================================================
    // ACTIONS SPÉCIALES
    // =========================================================

    public function resetPassword(int $id)
    {
        $user = User::findOrFail($id);

        $newPassword = Str::random(8) . rand(10, 99) . '!';
        $user->update(['password' => Hash::make($newPassword)]);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Mot de passe réinitialisé par l\'administrateur');

        return back()->with('success', "Mot de passe réinitialisé pour <strong>{$user->login}</strong>. Nouveau : <code>{$newPassword}</code>");
    }

    public function toggleVerrouillage(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->verouille) {
            $user->deverouiller();
            $message = "Compte <strong>{$user->login}</strong> déverrouillé.";
        } else {
            $user->suspendre('Verrouillé manuellement par l\'administrateur');
            $user->verouille = true;
            $user->save();
            $message = "Compte <strong>{$user->login}</strong> verrouillé.";
        }

        return back()->with('success', $message);
    }

    public function resendRHNotification(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->agent_completed) {
            return back()->with('info', 'Le dossier agent est déjà complété.');
        }

        $this->notifyRH($user);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Notification RH renvoyée');

        return back()->with('success', 'Notification envoyée aux agents RH et DRH.');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    protected function notifyRH(User $newUser): void
    {
        User::role(['AgentRH', 'DRH'])->each(
            fn(User $rh) => $rh->notify(new NouveauCompteARHNotification($newUser))
        );
    }
}
