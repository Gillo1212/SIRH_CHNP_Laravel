<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['AgentRH', 'DRH']);
    }

    public function rules(): array
    {
        $agentId = $this->route('agent') ?? $this->route('id');

        return [
            // Identité
            'matricule'          => ['required', 'string', 'max:20', "unique:agents,matricule,{$agentId},id_agent"],
            'nom'                => ['required', 'string', 'min:2', 'max:100'],
            'prenom'             => ['required', 'string', 'min:2', 'max:100'],
            'date_naissance'     => ['required', 'date', 'before:-18 years'],
            'lieu_naissance'     => ['nullable', 'string', 'max:100'],
            'sexe'               => ['required', 'in:M,F'],
            'situation_familiale'=> ['nullable', 'in:Célibataire,Marié,Divorcé,Veuf'],
            'nationalite'        => ['nullable', 'string', 'max:50'],

            // Statut (requis à la mise à jour)
            'statut_agent'       => ['required', 'in:Actif,En_congé,Suspendu,Retraité,Démissionnaire'],

            // Données sensibles (AES-256 ou personnelles)
            'adresse'            => ['nullable', 'string', 'max:255'],
            'telephone'          => ['nullable', 'string', 'max:20'],
            'email'              => ['nullable', 'email', 'max:150'],
            'religion'           => ['nullable', 'string', 'max:50'],
            'cni'                => ['nullable', 'string', 'max:50'],

            // Professionnel
            'date_prise_service' => ['nullable', 'date'],
            'fontion'            => ['nullable', 'string', 'max:100'],
            'grade'              => ['nullable', 'string', 'max:100'],
            'categorie_cp'       => ['nullable', 'in:Cadre_Superieur,Cadre_Moyen,Technicien_Superieur,Technicien,Agent_Administratif,Agent_de_Service,Commis_Administration,Ouvrier,Sans_Diplome'],
            'famille_d_emploi'   => ['nullable', 'string', 'max:100'],
            'id_service'         => ['nullable', 'integer', 'exists:services,id_service'],
            'id_division'        => ['nullable', 'integer', 'exists:divisions,id_division'],

            // Famille (tableaux dynamiques — conservés)
            'enfants'                          => ['nullable', 'array', 'max:15'],
            'enfants.*.prenom_complet'         => ['required_with:enfants.*.date_naissance_enfant', 'string', 'max:100'],
            'enfants.*.date_naissance_enfant'  => ['required_with:enfants.*.prenom_complet', 'date'],
            'enfants.*.lien_filiation'         => ['required_with:enfants.*.prenom_complet', 'in:Fils,Fille'],

            'conjoints'                       => ['nullable', 'array', 'max:1'],
            'conjoints.*.nom_conj'            => ['required_with:conjoints.*.prenom_conj', 'string', 'max:100'],
            'conjoints.*.prenom_conj'         => ['required_with:conjoints.*.nom_conj', 'string', 'max:100'],
            'conjoints.*.date_naissance_conj' => ['nullable', 'date'],
            'conjoints.*.type_lien'           => ['required_with:conjoints.*.nom_conj', 'in:Époux,Épouse'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'          => 'Le nom de famille est obligatoire.',
            'prenom.required'       => 'Le prénom est obligatoire.',
            'date_naissance.before' => 'L\'agent doit avoir au moins 18 ans.',
            'matricule.required'     => 'Le matricule est obligatoire.',
            'matricule.unique'       => 'Ce matricule est déjà utilisé.',
            'statut_agent.required'  => 'Le statut agent est obligatoire.',
            'statut_agent.in'        => 'Statut agent invalide.',
            'id_service.exists'     => 'Le service sélectionné n\'existe pas.',
            'id_division.exists'    => 'La division sélectionnée n\'existe pas.',
        ];
    }
}
