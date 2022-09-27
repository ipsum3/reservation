<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class StoreVehicule extends FormRequest
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

        return [
            "immatriculation" => 'required|max:255|unique:vehicules,immatriculation,'.(isset($current_params['vehicule']) ? $current_params['vehicule']->id : '').',id',
            "mise_en_circualtion_at" => "required|date_format:Y-m-d",
            "categorie_id" => "required|exists:categories,id",
            "marque_modele" => 'required|max:255',
            "sortie_at" => [
                "nullable",
                "date_format:Y-m-d",
                /*Rule::unique(Reservation::class)->where(function ($query) {
                    return $query->where('account_id', 1);
                }),*/
            ],
        ];
    }

}
