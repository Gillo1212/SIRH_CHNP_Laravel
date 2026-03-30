<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Manager');
    }

    public function rules(): array
    {
        return [
            'periode_debut' => 'required|date',
            'periode_fin'   => 'required|date|after_or_equal:periode_debut',
            'lignes'        => 'nullable|array',
            'lignes.*.id_agent'     => 'required|exists:agents,id_agent',
            'lignes.*.id_typeposte' => 'required|exists:type_postes,id_typeposte',
            'lignes.*.date_poste'   => 'required|date',
            'lignes.*.heure_debut'  => 'required|date_format:H:i',
            'lignes.*.heure_fin'    => 'required|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'periode_debut.required'       => 'La date de début est obligatoire.',
            'periode_fin.required'         => 'La date de fin est obligatoire.',
            'periode_fin.after_or_equal'   => 'La date de fin doit être après ou égale à la date de début.',
            'lignes.*.id_agent.required'   => 'Veuillez sélectionner un agent pour chaque ligne.',
            'lignes.*.id_typeposte.required' => 'Veuillez sélectionner un type de poste.',
            'lignes.*.date_poste.required' => 'La date de poste est obligatoire.',
            'lignes.*.heure_debut.required'=> 'L\'heure de début est obligatoire.',
            'lignes.*.heure_fin.required'  => 'L\'heure de fin est obligatoire.',
        ];
    }
}
