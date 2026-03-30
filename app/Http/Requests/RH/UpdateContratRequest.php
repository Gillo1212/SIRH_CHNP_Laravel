<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_contrat'   => 'required|in:PE,PCH,PU,Vacataire,CMSAS,Interne,Stagiaire',
            'date_debut'     => 'required|date',
            'date_fin'       => 'nullable|date|after:date_debut',
            'statut_contrat' => 'required|in:Actif,Expiré,Clôturé,En_renouvellement',
            'observation'    => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type_contrat.required'   => 'Le type de contrat est obligatoire.',
            'type_contrat.in'         => 'Le type de contrat sélectionné est invalide.',
            'date_debut.required'     => 'La date de début est obligatoire.',
            'date_debut.date'         => 'La date de début est invalide.',
            'date_fin.date'           => 'La date de fin est invalide.',
            'date_fin.after'          => 'La date de fin doit être postérieure à la date de début.',
            'statut_contrat.required' => 'Le statut du contrat est obligatoire.',
            'statut_contrat.in'       => 'Le statut sélectionné est invalide.',
        ];
    }

    public function attributes(): array
    {
        return [
            'type_contrat'   => 'type de contrat',
            'date_debut'     => 'date de début',
            'date_fin'       => 'date de fin',
            'statut_contrat' => 'statut',
        ];
    }
}
