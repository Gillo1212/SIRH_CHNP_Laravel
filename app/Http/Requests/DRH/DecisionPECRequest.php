<?php

namespace App\Http\Requests\DRH;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Décision DRH sur une prise en charge exceptionnelle (Intégrité CID).
 */
class DecisionPECRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('DRH') ?? false;
    }

    public function rules(): array
    {
        return [
            'action'      => 'required|in:approuver,rejeter',
            'motif_rejet' => 'required_if:action,rejeter|nullable|string|min:10|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'action.in'               => 'Action invalide.',
            'motif_rejet.required_if' => 'Le motif du rejet est obligatoire.',
            'motif_rejet.min'         => 'Le motif doit contenir au moins 10 caractères.',
        ];
    }
}
