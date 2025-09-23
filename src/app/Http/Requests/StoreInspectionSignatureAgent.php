<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Rules\InterventionUnique;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;

class StoreInspectionSignatureAgent extends FormRequest
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
            // Signatures
            'agent_signature' => ['required', 'string'],
        ];
    }

}
