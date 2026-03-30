<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMouvementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $type = $this->input('type_mouvement');

        return [
            'type_mouvement'    => 'required|in:Affectation initiale,Mutation,Retour,Départ',
            'date_mouvement'    => 'required|date',
            'motif'             => 'nullable|string|max:1000',
            'id_service'        => in_array($type, ['Affectation initiale', 'Mutation', 'Retour'])
                                    ? 'required|exists:services,id_service'
                                    : 'nullable|exists:services,id_service',
            'id_service_origine'=> in_array($type, ['Mutation', 'Retour'])
                                    ? 'required|exists:services,id_service|different:id_service'
                                    : 'nullable|exists:services,id_service',
        ];
    }

    public function messages(): array
    {
        return [
            'type_mouvement.required'      => 'Le type de mouvement est obligatoire.',
            'date_mouvement.required'      => "La date d'effet est obligatoire.",
            'id_service.required'          => 'Le service de destination est obligatoire.',
            'id_service_origine.required'  => "Le service d'origine est obligatoire.",
            'id_service_origine.different' => "Les services d'origine et de destination doivent être différents.",
        ];
    }
}
