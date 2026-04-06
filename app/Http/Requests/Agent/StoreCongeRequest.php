<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreCongeRequest — Validation demande de congé (Agent)
 * Intégrité CID : toutes les entrées validées avant traitement.
 */
class StoreCongeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'autorisation est gérée par les middlewares de route (auth + role/permission)
        return true;
    }

    public function rules(): array
    {
        return [
            'id_type_conge' => 'required|exists:type_conges,id_type_conge',
            'date_debut'    => 'required|date|after_or_equal:today',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'motif'         => 'nullable|string|max:500',
            'justificatif'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'id_type_conge.required'    => 'Veuillez sélectionner un type de congé.',
            'id_type_conge.exists'      => 'Le type de congé sélectionné est invalide.',
            'date_debut.required'       => 'La date de début est obligatoire.',
            'date_debut.after_or_equal' => 'La date de début ne peut pas être dans le passé.',
            'date_fin.required'         => 'La date de fin est obligatoire.',
            'date_fin.after_or_equal'   => 'La date de fin doit être après ou égale à la date de début.',
            'motif.max'                 => 'Le motif ne doit pas dépasser 500 caractères.',
            'justificatif.mimes'        => 'Le certificat médical doit être au format PDF, JPG ou PNG.',
            'justificatif.max'          => 'Le certificat médical ne doit pas dépasser 5 Mo.',
        ];
    }
}
