<?php

namespace App\Http\Requests\Agent;

use App\Models\DemandeDocument;
use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreDemandeDocumentRequest — Validation demande de document administratif (Agent)
 * Intégrité CID : toutes les entrées validées avant traitement.
 */
class StoreDemandeDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'autorisation est gérée par les middlewares de route (auth + role/permission)
        return true;
    }

    public function rules(): array
    {
        $typesValides = implode(',', array_keys(DemandeDocument::TYPES_DOCUMENTS));

        return [
            'type_document' => 'required|in:' . $typesValides,
            'motif'         => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type_document.required' => 'Veuillez sélectionner un type de document.',
            'type_document.in'       => 'Le type de document sélectionné est invalide.',
            'motif.max'              => 'Le motif ne doit pas dépasser 1000 caractères.',
        ];
    }
}
