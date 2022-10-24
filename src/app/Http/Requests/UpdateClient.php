<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Ipsum\Reservation\app\Models\Client;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Ipsum\Admin\app\Http\Requests\FormRequest;

class UpdateClient extends FormRequest
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
            //Rule::excludeIf($this->password === null),
            'password' => ['nullable', 'max:255', Password::min(8)->letters()->numbers()],
            'nom' => 'required|max:255',
            'prenom' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique(Client::class)->ignore($current_params['client']->id)],
            'telephone' => 'required|min:10',
            'adresse' => 'required',
            'cp' => 'required|max:255',
            'ville' => 'required|max:255',
            'pays_id' => 'required|exists:pays,id',
            'naissance_at' => [
                'required',
                'date_format:Y-m-d'
            ],
            'permis_numero' => 'required|max:255',
            'permis_at' => [
                'required',
                'date_format:Y-m-d'
            ],
            'permis_delivre' => 'required|max:255',
        ];
    }

}
