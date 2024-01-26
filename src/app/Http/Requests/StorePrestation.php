<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Ipsum\Reservation\app\Models\Prestation\Prestation;

class StorePrestation extends FormRequest
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

    protected function prepareForValidation()
    {
        $categories = collect($this->categories);
        $categories = $categories->filter(function ($value, $key) {
            return isset($value['has']);
        })->map(function ($value, $key) {
            unset($value['has']);
            return $value;
        });


        $lieux = collect($this->lieux);
        $lieux = $lieux->filter(function ($value, $key) {
            return isset($value['has']);
        })->map(function ($value, $key) {
            unset($value['has']);
            return $value;
        });

        $this->merge([
            'categories' => $categories,
            'lieux' => $lieux,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if (config('ipsum.reservation.prestation.custom_fields')) {
            foreach (config('ipsum.reservation.prestation.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        return [
            "type_id" => "required|exists:prestation_types,id",
            "tarification_id" => "required|exists:prestation_tarifications,id",
            "nom" => "required|max:255",
            "description" => "nullable",
            "montant" => "nullable|numeric",
            "quantite_max" => "required|numeric",
            "gratuit_apres" => "nullable|numeric",
            "jour_fact_max" => "nullable|numeric",
            "age_max" => "nullable|numeric|min:16|max:120",
            "jour" => ["nullable", Rule::in(array_keys(\Ipsum\Reservation\app\Models\Lieu\Horaire::JOURS))],
            "condition" => ["nullable", Rule::in(array_keys(Prestation::$LISTE_CONDITION))],
            "categorie_type_id" => ["nullable", Rule::exists(Type::class, 'id')],
            "heure_max" => ["nullable"],
            "heure_min" => ["nullable"],
            "duree_min" => ["nullable","numeric"],
            "duree_max" => ["nullable","numeric"],

            /*"categories.*.montant" => "numeric",
            "categories.*.montant" => "required|exists:categories,id",
            "lieux.*" => "required|exists:lieux,id",*/
        ] + $rules;
    }

}
