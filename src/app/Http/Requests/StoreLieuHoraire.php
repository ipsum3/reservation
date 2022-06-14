<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreLieuHoraire extends FormRequest
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
            "jour" => "required|integer|min:0|max:7",
            "debut" => "required|date_format:H:i",
            "fin" => "required|date_format:H:i",
        ];
    }

}
