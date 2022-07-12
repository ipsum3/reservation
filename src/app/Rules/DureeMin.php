<?php

namespace Ipsum\Reservation\app\Rules;


use Carbon\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class DureeMin implements InvokableRule, DataAwareRule
{
    protected $duree_minimum;

    protected $date_fin;

    protected $date_format;

    protected $data = [];


    public function __construct(int $duree_minimum, $date_fin, $date_format = 'd/m/Y H:i')
    {
        $this->duree_minimum = $duree_minimum;
        $this->date_fin = $date_fin;
        $this->date_format = $date_format;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {

        try {
            $date_debut = Carbon::createFromFormat($this->date_format, $this->data[$this->date_fin]);
            $date_fin = Carbon::createFromFormat($this->date_format, $value);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        if (Reservation::calculDuree($date_debut, $date_fin) < $this->duree_minimum) {
            $fail("La durée minimum d'une réservation sur le site est de ".$this->duree_minimum." jours. Merci de nous contacter si besoin.");
        }
    }
}