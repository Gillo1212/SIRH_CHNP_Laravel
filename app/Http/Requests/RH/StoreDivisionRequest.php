<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['AgentRH', 'DRH', 'AdminSystème']);
    }

    public function rules(): array
    {
        $divisionId = $this->route('id');

        return [
            'nom_division' => [
                'required', 'string', 'max:100',
                $divisionId
                    ? Rule::unique('divisions', 'nom_division')->ignore($divisionId, 'id_division')
                    : 'unique:divisions,nom_division',
            ],
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_division.required' => 'Le nom de la division est obligatoire.',
            'nom_division.unique'   => 'Une division avec ce nom existe déjà.',
        ];
    }
}
