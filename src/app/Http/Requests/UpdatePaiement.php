<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Ipsum\Admin\app\Http\Requests\FormRequest;

class UpdatePaiement extends FormRequest
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
            "created_at" => "required|date_format:Y-m-d",
            "paiement_moyen_id" => "required|integer|exists:paiement_moyens,id",
            "paiement_type_id" => "required|integer|exists:paiement_types,id",
            "montant" => "required|numeric",
            "note" => "nullable",
        ];
    }

}
