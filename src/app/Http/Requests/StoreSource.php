<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreSource extends FormRequest
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
            "nom" => "required|max:255",
            "type_id" => "required|exists:source_types,id"
        ];
    }

}
