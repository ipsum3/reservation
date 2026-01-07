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

    protected function prepareForValidation()
    {
        $prestations = collect($this->prestations);
        $prestations = $prestations->filter(function ($value, $key) {
            return isset($value['has']);
        })->map(function ($value, $key) {
            unset($value['has']);
            return $value;
        });

        $this->merge([
            'prestations' => $prestations,
        ]);
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
            "gps" => "max:255",
            "emails" => "required|array",
            "emails.*" => "email",
            "emails_reservation" => "required|array",
            'horaires' => 'nullable|array',
            'horaires.*.jour' => 'required|integer|min:0|max:7',
            'horaires.*.debut' => 'required|date_format:H:i',
            'horaires.*.fin' => 'required|date_format:H:i',

            "seo_title" => "nullable|max:255",
            "seo_description" => "nullable",
            "slug" => "nullable|max:255",

        ] + $rules;
    }

}
