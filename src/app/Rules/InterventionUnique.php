<?php

namespace Ipsum\Reservation\app\Rules;


use Ipsum\Reservation\app\Classes\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Ipsum\Reservation\app\Models\Categorie\Intervention;

class InterventionUnique implements Rule
{
    protected $vehicule_id;
    protected $debut_at;
    protected $fin_at;

    /**
     * Create a new rule instance.
     *
     * @param int $vehiculeId
     * @param string $debutAt
     * @param string $finAt
     */
    public function __construct($vehicule_id, $debut_at, $fin_at)
    {
        $this->vehicule_id = $vehicule_id;
        try {
            $this->debut_at = Carbon::parse($debut_at);
            $this->fin_at = Carbon::parse($fin_at);
        } catch (\InvalidArgumentException $e) {
            return;
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Intervention::where('vehicule_id', $this->vehicule_id)
            ->betweenDates($this->debut_at, $this->fin_at)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Une intervention existe déjà pour ce véhicule sur la période indiquée.';
    }
}