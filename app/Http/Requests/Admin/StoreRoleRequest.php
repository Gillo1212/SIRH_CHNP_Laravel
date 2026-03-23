<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('AdminSystème');
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:50', 'unique:roles,name', 'regex:/^[a-zA-ZÀ-ÿ_]+$/'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du rôle est obligatoire.',
            'name.unique'   => 'Ce rôle existe déjà.',
            'name.regex'    => 'Le nom ne doit contenir que des lettres et underscores (ex: chef_service).',
            'name.max'      => 'Le nom ne peut pas dépasser 50 caractères.',
        ];
    }
}
