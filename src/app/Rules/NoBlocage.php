<?php

namespace Ipsum\Reservation\app\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

class NoBlocage implements Rule
{

    protected $dateDebut;
    protected $dateFin;
    protected $lieu;
    protected $message = 'La catégorie est bloquée pour ce lieu et cette période.';


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(?Carbon $dateDebut, ?Carbon $dateFin, Lieu $lieu)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->lieu = $lieu;
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
        // $value est l'ID de la catégorie
        $categorie = Categorie::find($value);

        if (!$categorie || !$this->lieu) {
            $this->message = "Catégorie ou lieu introuvable.";
            return false;
        }

        try {
            return $categorie->hasNoBlocage($this->dateDebut, $this->dateFin, $this->lieu);
        } catch (\Exception $e) {
            $this->message = "Erreur de vérification de blocage.";
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
