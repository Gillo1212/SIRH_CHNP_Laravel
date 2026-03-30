<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['AgentRH', 'DRH', 'AdminSystème']);
    }

    public function rules(): array
    {
        return [
            'nom_service'       => 'required|string|max:100|unique:services,nom_service',
            'type_service'      => 'required|in:Clinique,Administratif,Aide_diagnostic,Support',
            'id_division'       => 'nullable|exists:divisions,id_division',
            'tel_service'       => 'nullable|string|max:20',
            'id_agent_manager'  => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_service.required' => 'Le nom du service est obligatoire.',
            'nom_service.unique'   => 'Un service avec ce nom existe déjà.',
            'type_service.required'=> 'Le type de service est obligatoire.',
            'type_service.in'      => 'Le type doit être : Clinique, Administratif, Aide_diagnostic ou Support.',
            'id_division.exists'   => 'La division sélectionnée n\'existe pas.',
            'id_agent_manager.exists' => 'L\'utilisateur sélectionné comme manager n\'existe pas.',
        ];
    }
}
