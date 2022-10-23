<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Prestation\Prestation;

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
            "client_id" => "nullable|integer|exists:clients,id",
            "etat_id" => "required|integer|exists:reservation_etats,id",
            "condition_paiement_id" => "required|integer|exists:condition_paiements,id",

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
            "vehicule_id" => [
                "nullable",
                "integer",
                Rule::exists(Vehicule::class, 'id')->where(function ($query) {
                    return $query->where('categorie_id', $this->categorie_id);
                })
            ],
            "vehicule_blocage" => "nullable|boolean",
            "caution" => "nullable|numeric",
            "franchise" => "nullable|numeric",
            "debut_at" => "required|date_format:Y-m-d\TH:i|before-or-equal:fin_at",
            "fin_at" => "required|date_format:Y-m-d\TH:i",
            "debut_lieu_id" => "required|integer|exists:lieux,id",
            "fin_lieu_id" => "required|integer|exists:lieux,id",

            "promotions.*.id" => "required|integer|exists:promotions,id",
            "promotions.*.nom" => "required|max:255",
            "promotions.*.reference" => "nullable|max:255",
            "promotions.*.reduction" => "required|numeric",

            "prestations.*.id" => "nullable|integer|exists:prestations,id",
            "prestations.*.quantite" => "nullable|integer",
            "prestations.*.tarif" => "nullable|numeric",
            "prestations.*.nom" => "nullable|max:255",
            "prestations.*.tarification" => ["nullable", Rule::in(Prestation::$LISTE_TARIFICATION)],

            "montant_base" => "nullable|numeric",
            "total" => "nullable|numeric",
            "montant_paye" => "nullable|numeric",

            "note" => "nullable",

            "paiements.*.created_at" => "nullable|date_format:Y-m-d",
            "paiements.*.paiement_moyen_id" => "nullable|integer|exists:paiement_moyens,id",
            "paiements.*.montant" => "nullable|numeric",
            "paiements.*.note" => "nullable",
        ] + $rules;
    }

}
