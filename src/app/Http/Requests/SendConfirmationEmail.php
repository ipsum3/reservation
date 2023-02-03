<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Ipsum\Reservation\app\Models\Client;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Ipsum\Admin\app\Http\Requests\FormRequest;

class SendConfirmationEmail extends FormRequest
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
            'reservation_id' => 'required',
            'email' => 'required|email',
        ];
    }

}
