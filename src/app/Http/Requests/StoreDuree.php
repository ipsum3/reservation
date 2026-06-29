<?php

namespace Ipsum\Reservation\app\Http\Requests;


use Illuminate\Validation\Rule;
use Ipsum\Admin\app\Http\Requests\FormRequest;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Jour;

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


    protected function prepareForValidation()
    {
        $jours_fin = collect($this->jours_fin);
        $jours_fin = $jours_fin->filter(function ($value, $key) {
            return isset($value['value']);
        });
        $jours_debut = collect($this->jours_debut);
        $jours_debut = $jours_debut->filter(function ($value, $key) {
            return isset($value['value']);
        });

        $this->merge([
            'jours_debut' => $jours_debut,
            'jours_fin' => $jours_fin,
        ]);

        $min = $this->durationToMinutes(
            $this->min_jours,
            $this->min_heures,
            $this->min_minutes
        );

        $this->merge([

            'min' => $min >= 1440 ?  $min - 1440 :  $min - 1,

            'max' => $this->durationToMinutes(
                $this->max_jours,
                $this->max_heures,
                $this->max_minutes
            ),

        ]);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'is_special' => 'nullable|boolean',
            'type' => 'nullable|max:255',
            'nom' => 'nullable|max:255',
            'tarification' => ['nullable', Rule::in(Duree::TARIFICATION)],
            'min' => 'required|integer|min:0',
            'max' => 'nullable|integer|gte:min',
            'min_jours' => 'required|integer|min:0',
            'min_heures' => 'required|integer|between:0,23',
            'min_minutes' => 'required|integer|between:0,59',
            'max_jours' => 'nullable|integer|min:0',
            'max_heures' => 'nullable|integer|between:0,23',
            'max_minutes' => 'nullable|integer|between:0,59',
            'jours_debut.*' => 'nullable|array',
            'jours_debut.*.value' => ['required', Rule::in(array_keys(Jour::VALEURS))],
            'jours_debut.*.heure' => 'nullable|date_format:H:i',
            'jours_fin.*' => 'nullable|array',
            'jours_fin.*.value' => ['required', Rule::in(array_keys(Jour::VALEURS))],
            'jours_fin.*.heure' => 'nullable|date_format:H:i',
        ];
    }

    protected function durationToMinutes($days, $hours, $minutes): ?int
    {
        if ($days === null && $hours === null && $minutes === null) {
            return null;
        }

        return ((int) $days * 1440)
            + ((int) $hours * 60)
            + (int) $minutes;
    }

}
