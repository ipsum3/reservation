<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Rules\VehiculeDisponible;

class StoreInspectionVehicule extends FormRequest
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
        $rules = [
            'categorie_id' => ['required', 'integer', 'exists:categories,id'],
            'vehicule_id' => [
                'required',
                'integer',
                Rule::exists(Vehicule::class, 'id')->where(function ($query) {
                    return $query->where('categorie_id', $this->categorie_id);
                })
            ],
            "vehicule_blocage" => "nullable|boolean",
        ];

        return $rules;
    }

}
