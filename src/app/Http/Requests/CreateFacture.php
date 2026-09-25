<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class CreateFacture extends FormRequest
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
        // TODO check produits : required
        return [
            /*"nom" => "required|max:255",
            "type_id" => "required|exists:source_types,id"*/

            "paiements.*.id" => "nullable|exists:paiements,id",
            "paiements.*.created_at" => "required|date_format:Y-m-d\TH:i",
            "paiements.*.paiement_moyen_id" => "required|integer|exists:paiement_moyens,id",
            "paiements.*.paiement_type_id" => "required|integer|exists:paiement_types,id",
            "paiements.*.reservation_id" => "required|integer|exists:reservations,id",
            "paiements.*.montant" => "required|numeric",
            "paiements.*.note" => "nullable",
        ];
    }

}
