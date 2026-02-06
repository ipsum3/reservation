<?php

namespace Ipsum\Reservation\app\Rules;

use Illuminate\Contracts\Validation\Rule;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class VehiculeDisponible implements Rule
{
    protected $reservation;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
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
        $vehicule = Vehicule::find($value);

        if (!$vehicule) {
            $this->message = "Le véhicule sélectionné n'existe pas.";
            return false;
        }

        // Récupération des conflits déjà prévue dans ton modèle
        try {
            $conflicts = $vehicule->getConflicts($this->reservation);

            if ($conflicts->isNotEmpty()) {
                $this->message = "Ce véhicule n'est pas disponible sur cette période.";
                return false;
            }

        } catch (\Exception $e) {
            $this->message = "Erreur de vérification de blocage.";
            return false;
        }

        return true;
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
