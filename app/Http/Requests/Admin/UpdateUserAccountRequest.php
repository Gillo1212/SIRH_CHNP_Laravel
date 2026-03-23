<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('AdminSystème');
    }

    public function rules(): array
    {
        $userId = $this->route('account');

        return [
            'login' => 'required|string|min:4|max:50|regex:/^[a-z0-9._-]+$/|unique:users,login,' . $userId,
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'login.unique'   => 'Ce login est déjà utilisé.',
            'login.regex'    => 'Le login ne peut contenir que des lettres minuscules, chiffres, points, tirets ou underscores.',
            'email.unique'   => 'Cet email est déjà utilisé.',
            'roles.required' => 'Vous devez sélectionner au moins un rôle.',
        ];
    }
}
