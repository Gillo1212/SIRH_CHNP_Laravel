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
        $agentId = $this->route('id');

        return [
            'nom'                 => ['required', 'string', 'min:2', 'max:100'],
            'prenom'              => ['required', 'string', 'min:2', 'max:100'],
            'date_naissance'      => ['required', 'date', 'before:-18 years'],
            'lieu_naissance'      => ['required', 'string', 'max:100'],
            'sexe'                => ['required', 'in:M,F'],
            'situation_familiale' => ['nullable', 'in:Célibataire,Marié,Divorcé,Veuf'],
            'nationalite'         => ['nullable', 'string', 'max:50'],
            'adresse'             => ['nullable', 'string', 'max:500'],
            'telephone'           => ['nullable', 'string', 'max:20'],
            'email'               => ['nullable', 'email', 'max:150', "unique:agents,email,{$agentId},id_agent"],
            'date_recrutement'    => ['required', 'date'],
            'fonction'            => ['nullable', 'string', 'max:100'],
            'grade'               => ['nullable', 'string', 'max:20'],
            'categorie_cp'        => ['nullable', 'in:Cadre_Superieur,Cadre_Moyen,Technicien_Superieur,Technicien,Agent_Administratif,Agent_de_Service,Commis_Administration,Ouvrier,Sans_Diplome'],
            'numero_assurance'    => ['nullable', 'string', 'max:50'],
            'statut'              => ['required', 'in:Actif,En_congé,Suspendu,Retraité'],
            'id_service'          => ['nullable', 'integer', 'exists:services,id_service'],
            'id_division'         => ['nullable', 'integer', 'exists:divisions,id_division'],
            'photo'               => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],

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
            'nom.required'              => 'Le nom de famille est obligatoire.',
            'prenom.required'           => 'Le prénom est obligatoire.',
            'date_naissance.before'     => 'L\'agent doit avoir au moins 18 ans.',
            'date_recrutement.required' => 'La date de recrutement est obligatoire.',
            'statut.required'           => 'Le statut est obligatoire.',
            'statut.in'                 => 'Statut invalide.',
            'email.unique'              => 'Cette adresse email est déjà utilisée.',
        ];
    }
}
