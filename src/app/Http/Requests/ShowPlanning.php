<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class ShowPlanning extends FormRequest
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
        $current_params = \Route::current()->parameters();

        return [
            "date_debut" => "nullable|date_format:Y-m-d",
            "date_fin" => "nullable|date_format:Y-m-d|after_or_equal:debut_at",
            "categorie_id" => "nullable|exists:categories,id",
        ];
    }

}
