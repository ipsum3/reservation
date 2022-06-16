<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
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
        return [
            "type_id" => "required|exists:prestation_types,id",
            "tarification" => ["required", Rule::in(Prestation::$LISTE_TARIFICATION)],
            "nom" => "required|max:255",
            "description" => "nullable",
            "montant" => "nullable|numeric",
            "quantite_max" => "required|numeric",
            "gratuit_apres" => "nullable|numeric",
            "jour_fact_max" => "nullable|numeric",

            /*"categories.*.montant" => "numeric",
            "categories.*.montant" => "required|exists:categories,id",
            "lieux.*" => "required|exists:lieux,id",*/
        ];
    }

}
