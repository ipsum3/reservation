<?php

namespace Ipsum\Reservation\app\Http\Requests\Caution;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class PreCreateFacture extends FormRequest
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
                "prenom" => "required",
                "telephone" => "required",
                "adresse" => "required",
                "cp" => "required",
                "ville" => "required",
                "pays_id" => "required",

                "montant_base" => "required",
                "total" => "required",
            ];
    }

}
