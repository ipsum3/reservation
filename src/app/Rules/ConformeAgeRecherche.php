<?php

namespace Ipsum\Reservation\app\Rules;


use Ipsum\Reservation\app\Classes\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class ConformeAgeRecherche implements InvokableRule
{
    protected $age_recherche;

    protected $date_format;


    public function __construct(int $age_recherche, $date_format = 'd/m/Y')
    {
        $this->age_recherche = $age_recherche;

        $this->date_format = $date_format;
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
            $date_naissance = Carbon::createFromFormat($this->date_format, $value);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        if ($date_naissance->age > $this->age_recherche ) {
            $fail("La date de naissance ne correspond pas à l'âge que vous avez renseigné lors de votre recherche. Vous avez renseigné ".$date_naissance->format('d/m/Y')." comme date. Si cette date de naissance est correcte nous vous invtons à refaire une recherche.");
        }
    }
}