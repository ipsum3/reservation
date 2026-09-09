<?php

namespace Ipsum\Reservation\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ipsum\Reservation\app\Models\Tarif\Duree;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DureeFactory extends Factory
{

    protected $model = Duree::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
		return [
			'nom' => 'test',
			'min' => 'test',
			'max' => 'test',
			'priorite' => 0,
		];
    }

}
