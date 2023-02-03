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
        $rules = [];

        if (config('ipsum.reservation.lieu.custom_fields')) {
            foreach (config('ipsum.reservation.lieu.custom_fields') as $field) {
                $rules['custom_fields.'.$field['name']] = $field['rules'];
            }
        }

        $current_params = \Route::current()->parameters();

        return [
            //Rule::excludeIf($this->password === null),
            'password' => ['nullable', 'max:255', Password::default()],
            'nom' => 'required|max:255',
            'prenom' => 'required|max:255',
            'email' => ['nullable', 'email', Rule::unique(Client::class)->ignore($current_params['client']->id)],
            'telephone' => 'nullable|min:10',
            'adresse' => 'nullable',
            'cp' => 'nullable|max:255',
            'ville' => 'nullable|max:255',
            'pays_id' => 'nullable|exists:pays,id',
            'naissance_at' => [
                'nullable',
                'date_format:Y-m-d'
            ],
            'permis_numero' => 'nullable|max:255',
            'permis_at' => [
                'nullable',
                'date_format:Y-m-d'
            ],
            'permis_delivre' => 'nullable|max:255',
        ] + $rules;
    }

}
