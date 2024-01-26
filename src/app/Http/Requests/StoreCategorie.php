<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreCategorie extends FormRequest
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
        $current_params = \Route::current()->parameters();

        $rules = [];

        if (config('ipsum.reservation.categorie.custom_fields')) {
            foreach (config('ipsum.reservation.categorie.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        return [
            "type_id" => "required|exists:categorie_types,id",

            "nb_vehicules" => 'nullable|integer',

            "nom" => 'required|max:255|unique:categories,nom,'.(isset($current_params['categorie']) ? $current_params['categorie']->id : '').',id',
            "modeles" => 'required|max:255',
            "description" => '',

            "place" => 'required|integer|min:1|max:50',
            "porte" => 'required|integer|min:1|max:10',
            "bagage" => 'nullable|integer|min:1|max:20',
            "volume" => 'nullable|integer',
            "reservoir_capacite" => 'nullable|numeric',
            "climatisation" => 'required|boolean',
            "transmission_id" => "required|exists:transmissions,id",
            "motorisation_id" => "required|exists:motorisations,id",
            "carrosseries.*" => "exists:carrosseries,id",
            "carrosseries" => "required",
            "lieux_exclus.*" => "exists:lieux,id",

            "caution" => 'nullable|numeric',
            "franchise" => 'nullable|numeric',
            "age_minimum" => 'required|numeric',
            "annee_permis_minimum" => 'required|numeric',

            "texte" => '',
            "seo_title" => 'nullable|max:255',
            "seo_description" => '',


        ] + $rules;
    }

}
