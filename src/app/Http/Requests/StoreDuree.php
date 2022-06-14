<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreDuree extends FormRequest
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
            'min' => 'required|numeric|min:0',
            'max' => 'nullable|numeric|gt:min',
        ];
    }

}
