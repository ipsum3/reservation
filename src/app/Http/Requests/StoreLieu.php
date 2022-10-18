<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreLieu extends FormRequest
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

        if (config('ipsum.reservation.lieu.custom_fields')) {
            foreach (config('ipsum.reservation.lieu.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        // TODO check gps
        return [
            "type_id" => "required|exists:lieu_types,id",

            "nom" => "required|max:255",
            "telephone" => "required|max:255",
            "adresse" => "required",
            "horaires_txt" => "required",
            "instruction" => "",
            "gps" => "required|max:255",
            "emails" => "required|array",
            "emails.*" => "email",
            "emails_reservation" => "required|array",

            "seo_title" => "nullable|max:255",
            "seo_description" => "nullable",
            "slug" => "nullable|max:255",

        ] + $rules;
    }

}
