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
                    'range_debut_at' => $date[0],
                    'range_fin_at' => $date[1],
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
            "debut_at" => "nullable|date_format:Y-m-d",
            "fin_at" => "nullable|date_format:Y-m-d",
            "range_debut_at" => "nullable|date_format:d/m/Y",
            "range_fin_at" => "nullable|date_format:d/m/Y",
            "lieu_id" => "nullable|exists:lieux,id",
        ];
    }


    protected function passedValidation()
    {
        if ($this->filled('debut_at')) {
            $debut_at = Carbon::createFromFormat('Y-m-d', $this->debut_at);
        } elseif ($this->filled('range_debut_at')) {
            $debut_at = Carbon::createFromFormat('d/m/Y', $this->range_debut_at);
        }else {
            $debut_at = Carbon::now();
        }

        if ($this->filled('fin_at')) {
            $fin_at = Carbon::createFromFormat('Y-m-d', $this->fin_at);
        } elseif ($this->filled('range_fin_at')) {
            $fin_at = Carbon::createFromFormat('d/m/Y', $this->range_fin_at);
        } else {
            $fin_at = $debut_at->clone();
        }

        $this->merge([
            'debut_at' => $debut_at->startOfDay(),
            'fin_at' => $fin_at->endOfDay()
        ]);
    }

}
