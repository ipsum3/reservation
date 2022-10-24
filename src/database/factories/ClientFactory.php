<?php

namespace Ipsum\Reservation\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ipsum\Reservation\app\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ClientFactory extends Factory
{

    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'civilite' => $this->faker->randomElement(['M.', 'Mme']),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->phoneNumber(),
            'adresse' => $this->faker->streetAddress(),
            'cp' => $this->faker->postcode(),
            'ville' => $this->faker->city(),
            'pays_id' => $this->faker->randomElement(['75', '136', '91']),
            'naissance_at' => $this->faker->dateTimeThisCentury(),
            'naissance_lieu' => $this->faker->city(),
            'permis_numero' => $this->faker->randomNumber(5, false),
            'permis_at' => $this->faker->dateTimeThisCentury(),
            'permis_delivre' => $this->faker->city(),

            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

}
