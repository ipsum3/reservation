<?php

namespace Ipsum\Reservation\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ipsum\Reservation\app\Models\Categorie\Categorie;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategorieFactory extends Factory
{

    protected $model = Categorie::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
		return [
			'type_id' => '1',
			'nom' => 'A',
			'modeles' => $this->faker->sentence(3),
			'description' => $this->faker->sentence(6),
			'texte' => $this->faker->sentence(50),
			'place' => rand(1, 8),
			'porte' => rand(3, 5),
			'bagage' => rand(3, 5),
			'climatisation' => rand(0, 1),
			'transmission_id' => rand(1, 2),
			'motorisation_id' => rand(1, 2),
			'franchise' => $this->faker->randomNumber(1),
			'age_minimum' => 21,
			'annee_permis_minimum' => 1,
		];
    }

}
