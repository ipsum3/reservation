<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Lieu\Ferie;
use Ipsum\Reservation\app\Models\Lieu\Fermeture;
use Ipsum\Reservation\app\Models\Lieu\Horaire;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Lieu\Type;


class LieuSeeder extends Seeder
{
    public function run()
    {

        foreach ($this->getFeries() as $data) {
            Ferie::create($data);
        }
        foreach ($this->getLieux() as $data) {
            Lieu::create($data);
        }

        foreach ($this->getFermetures() as $data) {
            Fermeture::create($data);
        }

        for ($i = 0; $i <= 6; $i++) {
            foreach ($this->getLieux() as $lieu) {
                Horaire::create(
                    [
                        'lieu_id' => $lieu['id'],
                        'jour' => $i,
                        'debut' => '07:00',
                        'fin' => '21:00',
                    ]
                );
            }
        }

        foreach ($this->getTypes() as $data) {
            Type::create($data);
        }
    }

    private function getLieux()
    {
        return array(
            array(
                'id' => 1,
                'type_id' => 1,
                'nom' => 'Aéroport Pôles Caraïbes',
                'telephone' => '00 00 00 00 00',
                'adresse' => 'Aéroport Pôles Caraïbes',
                'instruction' => '',
                'horaires_txt' => '',
                'gps' => '14.593218867470167,-61.00341788085939',
                'emails' => ['lieu1@example.com'],
                'emails_reservation' => ['lieu1@example.com'],
                'order' => '1'
            ),
            array(
                'id' => 2,
                'type_id' => 1,
                'nom' => 'Gare maritime de Pointe-à-Pitre',
                'telephone' => '00 00 00 00 00',
                'adresse' => 'Gare maritime de Pointe-à-Pitre',
                'instruction' => '',
                'horaires_txt' => '',
                'gps' => '14.593218867470167,-61.00341788085939',
                'emails' => ['lieu1@example.com'],
                'emails_reservation' => ['lieu1@example.com'],
                'order' => '2'
            ),
            array(
                'id' => 3,
                'type_id' => 1,
                'nom' => 'Agence Rev’Car à Jarry',
                'telephone' => '00 00 00 00 00',
                'adresse' => 'Agence Rev’Car à Jarry',
                'instruction' => '',
                'horaires_txt' => '',
                'gps' => '14.593218867470167,-61.00341788085939',
                'emails' => ['lieu1@example.com'],
                'emails_reservation' => ['lieu1@example.com'],
                'order' => '3'
            ),
            array(
                'id' => 4,
                'type_id' => 1,
                'nom' => 'Le Gosier',
                'telephone' => '00 00 00 00 00',
                'adresse' => 'Le Gosier',
                'instruction' => '',
                'horaires_txt' => '',
                'gps' => '14.593218867470167,-61.00341788085939',
                'emails' => [['lieu1@example.com']],
                'emails_reservation' => [['lieu1@example.com']],
                'order' => '3'
            ),
            array(
                'id' => 5,
                'type_id' => 2,
                'nom' => 'Autres',
                'telephone' => '00 00 00 00 00',
                'adresse' => 'Autres',
                'instruction' => '',
                'horaires_txt' => '',
                'gps' => '14.593218867470167,-61.00341788085939',
                'emails' => [['lieu1@example.com']],
                'emails_reservation' => [['lieu1@example.com']],
                'order' => '3'
            ),
        );
    }


    private function getFeries()
    {
        return array(
            array(
                'nom' => "Jour de l'an",
                'jour_at' => '2022-01-01',
            ),
            array(
                'nom' => "Lundi de Pâques",
                'jour_at' => '2022-04-02',
            ),
            array(
                'nom' => "Fête du Travail",
                'jour_at' => '2022-05-01',
            ),
            array(
                'nom' => "8 Mai 1945",
                'jour_at' => '2022-05-08',
            ),
            array(
                'nom' => "Jeudi de l'Ascension",
                'jour_at' => '2022-05-10',
            ),
            array(
                'nom' => "Lundi de Pentecôte",
                'jour_at' => '2022-05-21',
            ),
            array(
                'nom' => "Fête Nationale",
                'jour_at' => '2022-07-14',
            ),
            array(
                'nom' => "Assomption",
                'jour_at' => '2022-08-15',
            ),
            array(
                'nom' => "La Toussaint",
                'jour_at' => '2022-11-01',
            ),
            array(
                'nom' => "Armistice",
                'jour_at' => '2022-11-11',
            ),
            array(
                'nom' => "Noël",
                'jour_at' => '2022-12-25',
            ),
        );
    }

    private function getFermetures()
    {
        return array(
            array(
                'lieu_id' => '1',
                'nom' => "Test fermeture",
                'debut_at' => '2022-01-01',
                'fin_at' => '2022-01-01',
            ),
            array(
                'lieu_id' => '1',
                'nom' => "Test fermeture 2",
                'debut_at' => '2022-04-02',
            ),
        );
    }

    private function getTypes()
    {
        return array(
            array(
                'id' => '1',
                'nom' => "Agence",
            ),
            array(
                'id' => '2',
                'nom' => "Dépot",
            ),
        );
    }

}
