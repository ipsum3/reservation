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
        return !$this->facture or $this->facture->is_brouillon;
    }

    protected function prepareForValidation()
    {
        $this->reservation->loadMissing('entreprise');
        $this->replace($this->reservation->toArray());
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if (!$this->reservation->entreprise) {
            return [
                "client_id" => "required",
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

        return [
            "entreprise.nom" => "required",
            "entreprise.telephone" => "required",
            "entreprise.adresse" => "required",
            "entreprise.cp" => "required",
            "entreprise.ville" => "required",
            "entreprise.pays_id" => "required",
            "entreprise.vat_numero" => "required",
            "entreprise.siren" => "required",

            "montant_base" => "required",
            "total" => "required",
        ];
    }


    public function messages()
    {
        return [
            'client_id.required' => 'Un compte client est obligatoire pour créer une facture.',
        ];
    }

}
