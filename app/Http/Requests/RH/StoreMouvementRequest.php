<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class StoreMouvementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $type = $this->input('type_mouvement');

        return [
            'id_agent'          => 'required|exists:agents,id_agent',
            'type_mouvement'    => 'required|in:Affectation initiale,Mutation,Retour,Départ',
            'date_mouvement'    => 'required|date',
            'motif'             => 'nullable|string|max:1000',

            // Service destination — requis sauf pour Départ
            'id_service'        => in_array($type, ['Affectation initiale', 'Mutation', 'Retour'])
                                    ? 'required|exists:services,id_service'
                                    : 'nullable|exists:services,id_service',

            // Service origine — requis seulement pour Mutation et Retour
            'id_service_origine'=> in_array($type, ['Mutation', 'Retour'])
                                    ? 'required|exists:services,id_service|different:id_service'
                                    : 'nullable|exists:services,id_service',
        ];
    }

    public function messages(): array
    {
        return [
            'id_agent.required'            => 'Veuillez sélectionner un agent.',
            'id_agent.exists'              => "L'agent sélectionné est introuvable.",
            'type_mouvement.required'      => 'Le type de mouvement est obligatoire.',
            'type_mouvement.in'            => 'Type de mouvement invalide.',
            'date_mouvement.required'      => "La date d'effet est obligatoire.",
            'date_mouvement.date'          => "Format de date invalide.",
            'id_service.required'          => 'Le service de destination est obligatoire pour ce type de mouvement.',
            'id_service.exists'            => 'Le service de destination est introuvable.',
            'id_service_origine.required'  => "Le service d'origine est obligatoire pour une mutation ou un retour.",
            'id_service_origine.different' => "Le service d'origine et de destination doivent être différents.",
        ];
    }
}
