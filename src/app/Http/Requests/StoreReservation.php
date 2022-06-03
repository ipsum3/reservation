<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Reservation;

class StoreReservation extends FormRequest
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
        $types =  array_keys(config('ipsum.reservation.types'));
        $etats =  array_keys(config('ipsum.reservation.etats'));

        return [
            "categorie_id" => "nullable|integer|exists:reservation_categories,id",
            'titre' => 'required|max:255',
            'type' => 'required|in:'.implode(',', $types),
            'etat' => 'required|in:'.implode(',', $etats),
            'date' => 'nullable|date'
        ];
    }

}
