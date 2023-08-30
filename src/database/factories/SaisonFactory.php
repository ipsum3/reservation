<?php

namespace Ipsum\Reservation\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ipsum\Reservation\app\Models\Tarif\Saison;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SaisonFactory extends Factory
{

    protected $model = Saison::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
		return [
			'id' => '1',
			'nom' => 'test',
			'debut_at' => 'test',
			'fin_at' => 'test',
		];
    }

}
