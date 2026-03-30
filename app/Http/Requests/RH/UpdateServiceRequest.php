<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['AgentRH', 'DRH', 'AdminSystème']);
    }

    public function rules(): array
    {
        $serviceId = $this->route('id');

        return [
            'nom_service'  => ['required', 'string', 'max:100',
                Rule::unique('services', 'nom_service')->ignore($serviceId, 'id_service'),
            ],
            'type_service'     => 'required|in:Clinique,Administratif,Aide_diagnostic,Support',
            'tel_service'      => 'nullable|string|max:20',
            'id_agent_manager' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_service.required' => 'Le nom du service est obligatoire.',
            'nom_service.unique'   => 'Un service avec ce nom existe déjà.',
            'type_service.required'=> 'Le type de service est obligatoire.',
        ];
    }
}
