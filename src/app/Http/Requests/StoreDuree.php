<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Tarif\Duree;
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


    protected function prepareForValidation()
    {
        $jours_fin = collect($this->jours_fin);
        $jours_fin = $jours_fin->filter(function ($value, $key) {
            return isset($value['value']);
        });
        $jours_debut = collect($this->jours_debut);
        $jours_debut = $jours_debut->filter(function ($value, $key) {
            return isset($value['value']);
        });

        $this->merge([
            'jours_debut' => $jours_debut,
            'jours_fin' => $jours_fin,
        ]);
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
            'tarification' => ['nullable', Rule::in(Duree::TARIFICATION)],
            'min' => 'required|numeric|min:0',
            'max' => 'nullable|numeric|gte:min',
            'jours_debut.*' => 'nullable|array',
            'jours_debut.*.value' => ['required', Rule::in(array_keys(Jour::VALEURS))],
            'jours_debut.*.heure' => 'nullable|date_format:H:i',
            'jours_fin.*' => 'nullable|array',
            'jours_fin.*.value' => ['required', Rule::in(array_keys(Jour::VALEURS))],
            'jours_fin.*.heure' => 'nullable|date_format:H:i',
        ];
    }

}
