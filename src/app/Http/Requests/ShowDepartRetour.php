<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Carbon\Carbon;
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


    protected function prepareForValidation()
    {
        if ($this->filled('dates')) {
            $date = explode(' - ', $this->get('dates'));
            if (isset($date[1])) {
                $this->merge([
                    'debut_at' => $date[0],
                    'fin_at' => $date[1],
                ]);
            }
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "debut_at" => "nullable|date_format:d/m/Y",
            "fin_at" => "nullable|required_with:debut_at|date_format:d/m/Y",
            "lieu_id" => "nullable||exists:lieux,id",
        ];
    }


    protected function passedValidation()
    {
        if ($this->filled('debut_at')) {
            $debut_at = Carbon::createFromFormat('d/m/Y', $this->debut_at)->startOfDay();
            $fin_at = Carbon::createFromFormat('d/m/Y', $this->fin_at)->endOfDay();
        } else {
            $debut_at = Carbon::now()->startOfDay();
            $fin_at = Carbon::now()->endOfDay();
        }

        $this->merge([
            'debut_at' => $debut_at,
            'fin_at' => $fin_at
        ]);
    }

}
