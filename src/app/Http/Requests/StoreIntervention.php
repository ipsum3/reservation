<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Rules\InterventionUnique;
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
            // Rule unique car cela créé un bug graphique au niveau du calendrier => https://trello.com/c/YPLlrTKN/908-bug-reservation-v%C3%A9hicule
            "vehicule_id" => ["required", Rule::exists(Vehicule::class, 'id'), new InterventionUnique($this->vehicule_id, $this->debut_at, $this->fin_at, $this->intervention?->id)],
            "intervenant" => "nullable|max:255",
            "information" => "nullable|max:255",
            "km" => "nullable|numeric",
            "has_blocage" => "required|boolean",
            "debut_at" => "required|date_format:Y-m-d\TH:i|before-or-equal:fin_at",
            "fin_at" => "required|date_format:Y-m-d\TH:i"
        ];
    }

}
