<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;
use Illuminate\Validation\Validator;
use Ipsum\Reservation\app\Models\Categorie\Blocage;

class StoreCategorieBlocage extends FormRequest
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
            "categorie_id" => "required|exists:categories,id",
            "nom" => "nullable|max:255",
            "debut_at" => "required|date_format:Y-m-d|before-or-equal:fin_at",
            "fin_at" => "required|date_format:Y-m-d",
            "lieu_id" => "nullable|exists:lieux,id",
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $categorieId = $this->categorie_id;
            $debutAt = $this->debut_at;
            $finAt = $this->fin_at;

            // Vérification d’un chevauchement de période pour le même véhicule
            $existeBlocage = Blocage::where('categorie_id', $categorieId)
                ->where(function ($query) use ($debutAt, $finAt) {
                    $query->whereBetween('debut_at', [$debutAt, $finAt])
                        ->orWhereBetween('fin_at', [$debutAt, $finAt])
                        ->orWhere(function ($query) use ($debutAt, $finAt) {
                            $query->where('debut_at', '<=', $debutAt)
                                ->where('fin_at', '>=', $finAt);
                        });
                })
                ->exists();

            if ($existeBlocage) {
                $validator->errors()->add('categorie_id', 'Une intervention existe déjà pour cette catégorie sur la période indiquée.');
            }
        });
    }

}
