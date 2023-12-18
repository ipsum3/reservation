<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreSaison extends FormRequest
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

        if (config('ipsum.reservation.saison.custom_fields')) {
            foreach (config('ipsum.reservation.saison.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        return [
            "nom" => "required|max:255",
            "debut_at" => "required|date_format:Y-m-d|before-or-equal:fin_at",
            "fin_at" => "required|date_format:Y-m-d"
        ] + $rules;
    }

}
