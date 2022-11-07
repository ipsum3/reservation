<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreDuree extends FormRequest
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
            'is_special' => 'nullable|boolean',
            'type' => 'nullable|max:255',
            'nom' => 'nullable|max:255',
            'tarification' => 'nullable|in:forfait,jour',
            'min' => 'required|numeric|min:0',
            'max' => 'nullable|numeric|gte:min',
            'min_jour' => 'required_with:min_heure|nullable|integer|min:0|max:6',
            "min_heure" => "required_with:min_jour|date_format:H:i",
            'max_jour' => 'required_with:max_heure|nullable|integer|min:0|max:6',
            "max_heure" => "required_with:max_jour|nullable|date_format:H:i",
        ];
    }

}
