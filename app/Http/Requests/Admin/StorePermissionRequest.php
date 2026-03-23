<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('AdminSystème');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:permissions,name', 'regex:/^[a-z_]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la permission est obligatoire.',
            'name.unique'   => 'Cette permission existe déjà.',
            'name.regex'    => 'Format : minuscules et underscores uniquement (ex: voir_rapports).',
        ];
    }
}
