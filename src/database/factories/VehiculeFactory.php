<?php

namespace Ipsum\Reservation\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class VehiculeFactory extends Factory
{

    protected $model = Vehicule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $this->faker->addProvider(new \Faker\Provider\Fakecar($this->faker));
        $date = $this->faker->date();
        return [
            'immatriculation' => $this->faker->vehicleRegistration,
            'mise_en_circualtion_at' => $date,
            'categorie_id' => rand(1,5),
            'entree_at' => $date,
            'marque_modele' => $this->faker->vehicle,
        ];
    }

}
