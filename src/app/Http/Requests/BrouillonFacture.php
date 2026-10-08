<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class BrouillonFacture extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->facture or $this->facture->is_brouillon;
    }

    protected function prepareForValidation()
    {

        $produits = collect($this->produits)->mapWithKeys(function ($value) {
            return [$value['prestation_id'] => $value];
        })->toArray();

        $this->merge([
            'produits' => $produits,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            "echeance_at" => "required|date_format:Y-m-d",

            "produits" => "required|array",
            "produits.*.prestation_id" => "required|integer|exists:prestations,id|distinct",
            "produits.*.quantite" => "required|integer",
            "produits.*.montant" => "required|numeric",
            "produits.*.description" => "nullable",
            "produits.*.remise" => "nullable|numeric",

            "paiements.*.id" => "nullable|exists:paiements,id",
            "paiements.*.reservation_id" => "nullable|exists:reservations,id",
            "paiements.*.created_at" => "required|date_format:Y-m-d\TH:i",
            "paiements.*.paiement_moyen_id" => "required|integer|exists:paiement_moyens,id",
            "paiements.*.paiement_type_id" => "required|integer|exists:paiement_types,id",
            "paiements.*.montant" => "required|numeric",
            "paiements.*.note" => "nullable",
        ];
    }

}
