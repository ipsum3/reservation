<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Core\app\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        $settings = Setting::all()->pluck('key')->toArray();
        foreach ($this->getConfig() as $config) {
            if (!in_array($config['key'], $settings)) {
                Setting::create($config);
            }
        }

    }

    private function getConfig()
    {
        return array(
            array(
                'group' => 'Réservation',
                'key' => 'settings.reservation.duree_minimum',
                'name' => 'Durée minimum',
                'value' => '0',
                'type' => 'number',
                'rules' => 'required|numeric',
            ),
            array(
                'group' => 'Réservation',
                'key' => 'settings.reservation.duree_maximum',
                'name' => 'Durée maximum',
                'value' => '30',
                'type' => 'number',
                'rules' => 'required|numeric',
            ),
            array(
                'group' => 'Réservation',
                'key' => 'settings.reservation.delai_minimum',
                'name' => 'Délai minimum',
                'description' => 'Délai minimum en heure, entre la date de réservation et la date de départ.',
                'value' => '24',
                'type' => 'number',
                'rules' => 'required|numeric',
            ),
            array(
                'group' => 'Réservation',
                'key' => 'settings.reservation.delai_maximum',
                'name' => 'Délai maximum',
                'description' => 'Nombre de jours maximum entre la date de réservation et la date de départ.',
                'value' => '',
                'type' => 'number',
                'rules' => 'nullable|numeric',
            ),
            array(
                'group' => 'Réservation',
                'key' => 'settings.reservation.battement_entre_reservations',
                'name' => 'Battement entre 2 réservations',
                'description' => 'Durée minimum en heure, entre deux réservations.',
                'value' => '24',
                'type' => 'number',
                'rules' => 'nullable|numeric',
            ),
        );
    }
}
