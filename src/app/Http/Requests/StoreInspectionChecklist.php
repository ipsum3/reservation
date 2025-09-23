<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Rules\InterventionUnique;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;

class StoreInspectionChecklist extends FormRequest
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
        return [
            // Véhicule
            'kilometrage' => ['required', 'integer', 'min:0'],
            'carburant'   => ['required', 'integer', 'min:0', 'max:8'],

            // Checklist
            'checklists' => ['nullable', 'array'],
            'checklists.*' => ['nullable', 'exists:checklist,id'],
            'checklists.*.*' => ['nullable', 'boolean'],
            'observations' => ['nullable', 'string'],
        ];
    }

}
