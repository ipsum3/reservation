<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class ShowDepartRetour extends FormRequest
{

    protected $redirectRoute = 'admin.reservation.departEtRetour';

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
            "date" => "nullable|date_format:Y-m-d",
        ];
    }


    protected function prepareForValidation()
    {
        $this->mergeIfMissing(['date' => $this->date]);
    }

}
