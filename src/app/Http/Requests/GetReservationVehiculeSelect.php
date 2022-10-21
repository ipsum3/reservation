<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation;

class GetReservationVehiculeSelect extends FormRequest
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
            "categorie_id" => "required|integer|exists:categories,id",
            "debut_at" => "required|date_format:Y-m-d H:i:s",
            "fin_at" => "required|date_format:Y-m-d H:i:s",
        ];
    }

}
