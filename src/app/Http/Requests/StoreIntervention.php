<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;

class StoreIntervention extends FormRequest
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
            "type_id" => ["required", Rule::exists(InterventionType::class, 'id')],
            "vehicule_id" => ["required", Rule::exists(Vehicule::class, 'id')],
            "intervenant" => "nullable|max:255",
            "information" => "nullable|max:255",
            "debut_at" => "required|date_format:Y-m-d\TH:i|before-or-equal:fin_at",
            "fin_at" => "required|date_format:Y-m-d\TH:i"
        ];
    }

}
