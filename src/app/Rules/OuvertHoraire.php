<?php

namespace Ipsum\Reservation\app\Rules;


use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Lieu\Type;

class OuvertHoraire implements InvokableRule, DataAwareRule
{
    protected $lieu;

    protected $legende;

    protected $date_format;

    protected $data = [];


    public function __construct($lieu, $legende, $date_format = 'd/m/Y H:i')
    {
        $this->lieu = $lieu;
        $this->legende = $legende;
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

        if (!isset($this->data[$this->lieu])) {
            return;
        }

        $lieu = Lieu::find($this->data[$this->lieu]);

        if(!$lieu) {
            return;
        }

        try {
            $date = Carbon::createFromFormat($this->date_format, $value);
        } catch (\InvalidArgumentException $e) {
            return;
        }


        if (!$lieu->isOuvertHoraire($date)) {
            $creneaux = $lieu->creneauxHorairesToString($date);
            $horaires = $creneaux ? _("Il est ouvert ce jour là ").$creneaux.'.' : _("Veuillez choisir une autre date.");

            $fail($lieu->type_id === Type::DEPOT_ID ? "Les :lieus se font uniquement $creneaux sur ce lieu le :date" : "Le lieu de :lieu est fermé le :date à :heure. :horaires")->translate([
                'lieu' => $this->legende,
                'date' => $date->format('d/m/Y'),
                'heure' => $date->format('H\hi'),
                'horaires' => $horaires,
            ]);
        }
    }
}