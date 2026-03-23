<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('AdminSystème');
    }

    public function rules(): array
    {
        return [
            'login'     => 'required|string|min:4|max:50|unique:users,login|regex:/^[a-z0-9._-]+$/',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'roles'     => 'required|array|min:1',
            'roles.*'   => 'exists:roles,name',
            'notify_rh' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'login.unique'   => 'Ce login est déjà utilisé.',
            'login.regex'    => 'Le login ne peut contenir que des lettres minuscules, chiffres, points, tirets ou underscores.',
            'email.unique'   => 'Cet email est déjà utilisé.',
            'password.min'   => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'roles.required' => 'Vous devez sélectionner au moins un rôle.',
        ];
    }
}
