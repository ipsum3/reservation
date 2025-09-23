<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Rules\InterventionUnique;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;

class StoreInspectionDommage extends FormRequest
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
            // Dommages
            'ids' => ['nullable', 'array'],
            'dommages' => ['nullable', 'array'],
            'dommages.*.id' => ['nullable', 'integer'],
            'dommages.*.uuid' => ['nullable', 'string'],
            'dommages.*.type_id' => ['required_with:dommages', 'integer', 'exists:dommage_types,id'],
            'dommages.*.emplacement_id' => ['required_with:dommages', 'integer', 'exists:dommage_emplacements,id'],
            'dommages.*.element_id' => ['required_with:dommages', 'integer', 'exists:dommage_elements,id'],
            'dommages.*.observations' => ['nullable', 'string'],
        ];
    }

}
