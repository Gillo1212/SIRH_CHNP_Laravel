<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Manager');
    }

    public function rules(): array
    {
        return [
            'id_agent'     => 'required|exists:agents,id_agent',
            'date_absence' => 'required|date|before_or_equal:today',
            'type_absence' => 'required|in:Maladie,Personnelle,Professionnelle,Injustifiée',
            'justifie'     => 'boolean',
            'commentaire'  => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'id_agent.required'     => 'Veuillez sélectionner un agent.',
            'id_agent.exists'       => 'L\'agent sélectionné n\'existe pas.',
            'date_absence.required' => 'La date d\'absence est obligatoire.',
            'date_absence.before_or_equal' => 'La date d\'absence ne peut pas être dans le futur.',
            'type_absence.required' => 'Le type d\'absence est obligatoire.',
            'type_absence.in'       => 'Type invalide. Choisir : Maladie, Personnelle, Professionnelle ou Injustifiée.',
        ];
    }
}
