<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StorePECRequest — Validation demande de prise en charge médicale (Agent)
 * Intégrité CID : toutes les entrées validées avant traitement.
 */
class StorePECRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'autorisation est gérée par les middlewares de route (auth + role/permission)
        return true;
    }

    public function rules(): array
    {
        return [
            'ayant_droit'    => 'required|string|in:Agent,Conjoint,Enfant',
            'type_prise'     => 'required|string|max:255',
            'raison_medical' => 'required|string|max:1000',
            'justificatif'   => 'required_if:ayant_droit,Conjoint|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'ayant_droit.required'      => 'Veuillez sélectionner le bénéficiaire.',
            'ayant_droit.in'            => 'Le bénéficiaire doit être Agent, Conjoint ou Enfant.',
            'type_prise.required'       => 'Le type de prise en charge est obligatoire.',
            'raison_medical.required'   => 'La raison médicale est obligatoire.',
            'raison_medical.max'        => 'La raison médicale ne doit pas dépasser 1000 caractères.',
            'justificatif.required_if'  => 'Le certificat de mariage est obligatoire pour une prise en charge du conjoint.',
            'justificatif.mimes'        => 'Le justificatif doit être au format PDF, JPG ou PNG.',
            'justificatif.max'          => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ];
    }
}
