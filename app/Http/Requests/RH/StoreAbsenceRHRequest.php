<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRHRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole(['AgentRH', 'DRH']);
    }

    public function rules(): array
    {
        return [
            'id_agent'     => ['required', 'exists:agents,id_agent'],
            'date_absence' => ['required', 'date', 'before_or_equal:today'],
            'type_absence' => ['required', 'in:Maladie,Personnelle,Professionnelle,Injustifiée'],
            'justifie'     => ['nullable', 'boolean'],
            'commentaire'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_agent.required'           => 'Veuillez sélectionner un agent.',
            'id_agent.exists'             => 'Agent introuvable.',
            'date_absence.required'       => "La date d'absence est obligatoire.",
            'date_absence.before_or_equal'=> 'La date ne peut pas être dans le futur.',
            'type_absence.required'       => "Le type d'absence est obligatoire.",
            'type_absence.in'             => "Type d'absence invalide.",
            'commentaire.max'             => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ];
    }
}
