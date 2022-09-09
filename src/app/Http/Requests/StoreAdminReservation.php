<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Reservation;

class StoreAdminReservation extends FormRequest
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
        $rules = [];

        if (config('ipsum.reservation.custom_fields')) {
            foreach (config('ipsum.reservation.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        return [
            "etat_id" => "required|integer|exists:reservation_etats,id",
            "modalite_paiement_id" => "required|integer|exists:modalite_paiements,id",

            "civilite" => "nullable|in:M.,Mme",
            "nom" => "required|max:255",
            "prenom" => "nullable|max:255",
            "email" => "nullable|email|max:255",
            "telephone" => "nullable|max:255",
            "adresse" => "nullable|max:255",
            "cp" => "nullable|max:255",
            "ville" => "nullable|max:255",
            "pays_id" => "nullable|integer|exists:pays,id",
            "naissance_at" => "nullable|date_format:Y-m-d",
            'naissance_lieu' => 'nullable|max:255',
            "permis_numero" => "nullable|max:255",
            "permis_at" => "nullable|date_format:Y-m-d",
            "permis_delivre" => "nullable|max:255",
            "observation" => "nullable",
            "datas" => "nullable|array",

            "categorie_id" => "required|integer|exists:categories,id",
            "franchise" => "nullable|numeric",
            "debut_at" => "required|date_format:Y-m-d H:i:s",
            "fin_at" => "required|date_format:Y-m-d H:i:s",
            "debut_lieu_id" => "required|integer|exists:lieux,id",
            "fin_lieu_id" => "required|integer|exists:lieux,id",

            "montant_base" => "nullable|numeric",
            "total" => "nullable|numeric",
            "montant_paye" => "nullable|numeric",

            "note" => "nullable",
        ] + $rules;
    }

}
