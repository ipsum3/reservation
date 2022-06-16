<?php

namespace Ipsum\Reservation\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReservationFactory extends Factory
{

    protected $model = Reservation::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $arrivee_at = \Carbon\Carbon::now()->addDays(rand(-30, 60));
        $total = $this->faker->randomFloat(2, 1, 300);

		return [
			'etat_id' => rand(1, 3),
			'modalite_paiement_id' => rand(1, 2),
			'nom' => $this->faker->lastName,
			'prenom' => $this->faker->firstName,
			'email' => $this->faker->email,
			'telephone' => $this->faker->phoneNumber,
			'observation' => $this->faker->optional($weight = 0.2)->sentence(6),

            'categorie_id' => '1',
            'categorie_nom' => 'A',
            'franchise' => 300,
            'debut_at' => $arrivee_at,
            'fin_at' => $arrivee_at->addDays(rand(1, 15)),
            'debut_lieu_id' => rand(1, 3),
            'fin_lieu_id' => rand(1, 3),
            'debut_lieu_nom' => 'lieu',
            'fin_lieu_nom' => 'lieu',

            'montant_base' => $this->faker->randomFloat(2, 1, 300),
            'total' => $total,
            'montant_paye' => $total,
		];
    }

}
