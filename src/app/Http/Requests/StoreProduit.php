<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Ipsum\Reservation\app\Models\Prestation\Prestation;

class StoreProduit extends FormRequest
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
        $rules = [];

        if (config('ipsum.reservation.prestation.custom_fields')) {
            foreach (config('ipsum.reservation.prestation.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        return [
            "nom" => "required|max:255",
            "reference_externe" => "nullable|max:255",
            "montant" => "nullable|numeric",
            "type_id" => "required|exists:prestation_types,id",
            "taxe_id" => "required|exists:taxes,id",

        ] + $rules;
    }

}
