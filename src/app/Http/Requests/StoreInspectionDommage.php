<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Ipsum\Admin\app\Http\Requests\FormRequest;

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
            'type_id' => ['required_with:dommages', 'integer', 'exists:dommage_types,id'],
            'emplacement_id' => ['required_with:dommages', 'integer', 'exists:dommage_emplacements,id'],
            'element_id' => ['required_with:dommages', 'integer', 'exists:dommage_elements,id'],
            'observations' => ['nullable', 'string'],
        ];
    }

}
