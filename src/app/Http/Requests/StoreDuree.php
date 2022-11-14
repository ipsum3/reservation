<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Tarif\Jour;

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
            'jours.*' => 'nullable|array',
            'jours.*.value' => ['required', Rule::in(array_keys(Jour::VALEURS))],
            'jours.*.heure_debut_min' => 'nullable|date_format:H:i',
            'jours.*.heure_fin_max' => 'nullable|date_format:H:i',
        ];
    }

}
