<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Promotion\Promotion;

class StorePromotion extends FormRequest
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


        $prestations = collect($this->prestations);
        $prestations = $prestations->filter(function ($value, $key) {
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
            'prestations' => $prestations,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $current_params = \Route::current()->parameters();

        return [
            "client_id" => ['nullable', Rule::exists(config('ipsum.reservation.client.model'), 'id')],
            "type" => 'required|in:reduction',

            "reference" => 'nullable|max:255|unique:promotions,reference,'.(isset($current_params['promotion']) ? $current_params['promotion']->id : '').',id',
            "nom" => 'required|max:255',
            'extrait' => 'nullable',
            'texte' => 'nullable',

            "modalite_paiement_id" => "nullable|exists:modalite_paiements,id",
            "code" => 'nullable|max:255|unique:promotions,code,'.(isset($current_params['promotion']) ? $current_params['promotion']->id : '').',id',

            'debut_at' => 'required|date',
            'fin_at' => 'required|date|after_or_equal:debut_at',
            'activation_at' => 'nullable|date',
            'desactivation_at' => 'nullable|date|after_or_equal:activation_at',

            "duree_min" => 'nullable|numeric',
            "duree_max" => 'nullable|numeric',

            "reduction_type" => 'nullable|in:'.implode(',', array_keys(Promotion::REDUCTION_TYPES)),
            "reduction_valeur" => 'nullable|numeric',

            "prestations" => 'prohibited_if:reduction_type,pourcentage',

            "seo_title" => 'nullable|max:255',
            "seo_description" => 'nullable',
        ];
    }

}
