<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\User;
use App\Notifications\NouveauDossierAgentNotification;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AgentService
{
    public function __construct(
        private AgentRepositoryInterface $repo
    ) {}

    /**
     * Créer un dossier agent SANS compte utilisateur.
     * Workflow : RH crée le dossier → Admin reçoit notification → Admin crée le compte.
     * Intégrité CID : transaction DB atomique
     */
    public function creerAgent(array $data, ?UploadedFile $photo = null): Agent
    {
        // Détecter le workflow avant la transaction
        $userId = !empty($data['user_id']) ? (int) $data['user_id'] : null;

        // Transaction : uniquement les écritures DB (Intégrité CID)
        $agent = DB::transaction(function () use ($data, $photo, $userId) {

            // 1. Générer le matricule
            $matricule = $this->repo->nextMatricule();

            // 2. Traiter la photo
            $photoPath = $this->sauvegarderPhoto($photo);

            // 3. Créer l'agent (champs sensibles chiffrés via casts)
            $agent = $this->repo->create([
                'user_id'            => $userId,
                'matricule'          => $matricule,
                'nom'                => strtoupper(trim($data['nom'])),
                'prenom'             => ucwords(strtolower(trim($data['prenom']))),
                'date_naissance'     => $data['date_naissance'],
                'lieu_naissance'     => $data['lieu_naissance'],
                'sexe'               => $data['sexe'],
                'situation_familiale'=> $data['situation_familiale'] ?? null,
                'nationalite'        => $data['nationalite'] ?? 'Sénégalaise',
                'adresse'            => $data['adresse'] ?? null,       // Auto-chiffré AES-256
                'telephone'          => $data['telephone'] ?? null,     // Auto-chiffré AES-256
                'email'              => $data['email'] ?? null,
                'date_recrutement'   => $data['date_recrutement'],
                'fonction'           => $data['fonction'] ?? null,
                'grade'              => $data['grade'] ?? null,
                'categorie_cp'       => $data['categorie_cp'] ?? null,
                'numero_assurance'   => $data['numero_assurance'] ?? null, // Auto-chiffré AES-256
                'statut'             => 'Actif',
                // Workflow RH-first : en attente de création du compte par Admin
                'account_pending'    => $userId ? false : true,
                'photo'              => $photoPath,
                'id_service'         => $data['id_service'] ?? null,
                'id_division'        => $data['id_division'] ?? null,
            ]);

            // 4. Synchroniser la famille
            $this->syncEnfants($agent, $data['enfants'] ?? []);
            $this->syncConjoints($agent, $data['conjoints'] ?? []);

            // 5. Workflow Admin-first : marquer le compte comme complété
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->update([
                        'agent_completed' => true,
                        'name'            => $agent->prenom . ' ' . $agent->nom,
                    ]);

                    activity()
                        ->causedBy(auth()->user())
                        ->performedOn($user)
                        ->withProperties(['matricule' => $agent->matricule])
                        ->log("Dossier agent complété par la RH — matricule {$agent->matricule}");
                }
            }

            // 6. Audit trail (Intégrité CID) — dans la transaction pour garantir la cohérence
            activity()
                ->causedBy(auth()->user())
                ->performedOn($agent)
                ->withProperties([
                    'matricule'     => $agent->matricule,
                    'statut_compte' => $userId ? 'compte_lie' : 'en_attente',
                ])
                ->log($userId
                    ? "Dossier agent créé et lié au compte utilisateur ID {$userId}"
                    : 'Dossier agent créé — compte utilisateur en attente de création'
                );

            return $agent;
        });

        // 7. Notifications HORS transaction (effet de bord — ne doit pas annuler la création)
        if (!$userId) {
            try {
                User::role('AdminSystème')->each(function (User $admin) use ($agent) {
                    $admin->notify(new NouveauDossierAgentNotification($agent));
                });
            } catch (\Throwable $e) {
                // Ne pas bloquer la création si la notification échoue
                \Illuminate\Support\Facades\Log::warning(
                    "Notification AdminSystème non envoyée pour l'agent {$agent->matricule} : " . $e->getMessage()
                );
            }
        }

        return $agent;
    }

    /**
     * Modifier un agent
     * Intégrité CID : transaction DB atomique
     */
    public function modifierAgent(int $id, array $data, ?UploadedFile $photo = null): Agent
    {
        return DB::transaction(function () use ($id, $data, $photo) {

            $agent = $this->repo->findById($id);

            // Traiter la nouvelle photo si fournie
            $photoPath = $agent->photo;
            if ($photo) {
                // Supprimer l'ancienne
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $this->sauvegarderPhoto($photo);
            }

            // Mettre à jour l'agent
            $agent = $this->repo->update($id, [
                'nom'                => strtoupper(trim($data['nom'])),
                'prenom'             => ucwords(strtolower(trim($data['prenom']))),
                'date_naissance'     => $data['date_naissance'],
                'lieu_naissance'     => $data['lieu_naissance'],
                'sexe'               => $data['sexe'],
                'situation_familiale'=> $data['situation_familiale'] ?? null,
                'nationalite'        => $data['nationalite'] ?? 'Sénégalaise',
                'adresse'            => $data['adresse'] ?? null,
                'telephone'          => $data['telephone'] ?? null,
                'email'              => $data['email'] ?? null,
                'date_recrutement'   => $data['date_recrutement'],
                'fonction'           => $data['fonction'] ?? null,
                'grade'              => $data['grade'] ?? null,
                'categorie_cp'       => $data['categorie_cp'] ?? null,
                'numero_assurance'   => $data['numero_assurance'] ?? null,
                'statut'             => $data['statut'],
                'photo'              => $photoPath,
                'id_service'         => $data['id_service'] ?? null,
                'id_division'        => $data['id_division'] ?? null,
            ]);

            // Synchroniser la famille
            $this->syncEnfants($agent, $data['enfants'] ?? []);
            $this->syncConjoints($agent, $data['conjoints'] ?? []);

            // Mettre à jour le nom dans la table users si le compte existe
            $agent->user?->update(['name' => $data['prenom'] . ' ' . $data['nom']]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($agent)
                ->log('Modification agent');

            return $agent;
        });
    }

    /**
     * Supprimer un agent (soft delete)
     */
    public function supprimerAgent(int $id): void
    {
        DB::transaction(function () use ($id) {
            $agent = $this->repo->findById($id);

            // Désactiver le compte utilisateur s'il existe
            $agent->user?->update(['statut_compte' => 'Inactif']);

            $this->repo->delete($id);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($agent)
                ->withProperties(['matricule' => $agent->matricule])
                ->log('Suppression agent');
        });
    }

    /**
     * Upload et redimensionnement photo 200×200px
     * Disponibilité CID : stockage optimisé
     */
    private function sauvegarderPhoto(?UploadedFile $file): ?string
    {
        if (!$file) return null;

        $filename = 'agents/' . uniqid('photo_') . '.jpg';
        $path = storage_path('app/public/' . $filename);

        // Assurer que le dossier existe
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        Image::read($file)
            ->cover(200, 200)
            ->toJpeg(85)
            ->save($path);

        return $filename;
    }

    /**
     * Synchroniser les enfants (delete + insert)
     * Intégrité CID : remplace tous les enfants en une transaction
     */
    private function syncEnfants(Agent $agent, array $enfants): void
    {
        $agent->enfants()->delete();

        foreach ($enfants as $e) {
            if (empty($e['prenom_complet']) || empty($e['date_naissance_enfant'])) continue;

            $agent->enfants()->create([
                'prenom_complet'        => trim($e['prenom_complet']),
                'date_naissance_enfant' => $e['date_naissance_enfant'],
                'lien_filiation'        => $e['lien_filiation'],
            ]);
        }
    }

    /**
     * Synchroniser les conjoints (delete + insert)
     */
    private function syncConjoints(Agent $agent, array $conjoints): void
    {
        $agent->conjoints()->delete();

        foreach ($conjoints as $c) {
            if (empty($c['nom_conj']) || empty($c['prenom_conj'])) continue;

            $agent->conjoints()->create([
                'nom_conj'           => strtoupper(trim($c['nom_conj'])),
                'prenom_conj'        => ucwords(strtolower(trim($c['prenom_conj']))),
                'date_naissance_conj'=> $c['date_naissance_conj'] ?? null,
                'type_lien'          => $c['type_lien'],
            ]);
        }
    }

    /**
     * Générer un login unique : prenom.nom (normalisé sans accents)
     */
    private function genererLogin(string $prenom, string $nom): string
    {
        $base = Str::ascii(strtolower($prenom)) . '.' . Str::ascii(strtolower($nom));
        $base = preg_replace('/[^a-z.]/', '', $base);
        $login = $base;
        $i = 1;

        while (!$this->repo->loginDisponible($login)) {
            $login = $base . $i;
            $i++;
        }

        return $login;
    }
}
