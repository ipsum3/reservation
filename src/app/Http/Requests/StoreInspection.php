<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Rules\InterventionUnique;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;

class StoreInspection extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //dd($this->request);
        return [
            // Véhicule
            'kilometrage' => ['nullable', 'integer', 'min:0'],
            'carburant'   => ['nullable', 'integer', 'min:0', 'max:8'],
            'categorie_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'vehicule_id' => ['sometimes', 'required', 'integer', 'exists:vehicules,id'],
            "vehicule_blocage" => "nullable|boolean",

            // Checklist
            'checklists' => ['nullable', 'array'],
            'checklists.*' => ['nullable', 'exists:checklist,id'],
            'checklists.*.*' => ['nullable', 'boolean'],
            'observations' => ['nullable', 'string'],

            // Dommages
            'dommages' => ['nullable', 'array'],
            'dommages.*.id' => ['nullable', 'integer'],
            'dommages.*.uuid' => ['nullable', 'string'],
            'dommages.*.type_id' => ['required_with:dommages', 'integer', 'exists:dommage_types,id'],
            'dommages.*.emplacement_id' => ['required_with:dommages', 'integer', 'exists:dommage_emplacements,id'],
            'dommages.*.element_id' => ['required_with:dommages', 'integer', 'exists:dommage_elements,id'],
            'dommages.*.observations' => ['nullable', 'string'],
            'dommages.*.image' => ['nullable', 'string'],

            // Signatures
            'agent_signature' => ['nullable', 'string'],
            'locataire_signature' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'kilometrage.required' => 'Le kilométrage est obligatoire.',
            'kilometrage.numeric' => 'Le kilométrage doit être un nombre.',
            'carburant.required' => 'Le niveau de carburant est obligatoire.',
            'carburant.numeric' => 'Le niveau de carburant doit être un nombre.',
            'carburant.max' => 'Le niveau de carburant ne peut pas dépasser 100%.',

            'dommages.*.type_id.required_with' => 'Le type de dommage est obligatoire.',
            'dommages.*.emplacement_id.required_with' => 'L’emplacement est obligatoire.',
            'dommages.*.element_id.required_with' => 'L’élément est obligatoire.',
        ];
    }

}
