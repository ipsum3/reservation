<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use PHPUnit\Framework\TestCase;

class SaisonTest extends TestCase
{

    protected function getRerevations()
    {
        return [

            // test exist saison
            [ '2023-08-30 16:00', '2023-08-31 23:59', 0 ],
            [ '2023-09-10 00:00', '2023-09-11 00:00', 0 ],

            // Une seule saison
            [ '2023-09-01 00:00', '2023-09-01 00:01', 1 ],
            [ '2023-09-01 00:00', '2023-09-02 00:00', 1 ],
            [ '2023-09-01 00:00', '2023-09-09 00:00', 8 ],
            [ '2023-09-01 00:00', '2023-09-08 23:59', 8 ],
            [ '2023-09-01 16:00', '2023-09-09 16:00', 8 ],
            [ '2023-09-01 16:00', '2023-09-09 17:00', 8 ],
            [ '2023-09-01 16:00', '2023-09-09 17:01', 9 ],

            // Saison intermédiaire
            [ '2023-08-30 16:00', '2023-09-11 16:00', 9 ],
            [ '2023-09-01 00:00', '2023-09-09 23:59', 9 ],

            // Dernière saison
            [ '2023-08-30 16:00', '2023-09-02 16:00', 1 ],
            [ '2023-08-30 16:00', '2023-09-02 17:00', 1 ],
            [ '2023-08-30 16:00', '2023-09-02 17:01', 2 ],
            [ '2023-08-30 16:00', '2023-09-01 17:00', 0 ],
            [ '2023-08-30 16:00', '2023-09-01 17:01', 1 ],

            // Première saison
            [ '2023-09-09 23:59', '2023-09-11 16:00', 1 ],
            [ '2023-09-08 16:00', '2023-09-11 16:00', 2 ],
            [ '2023-09-09 16:00', '2023-09-11 16:00', 1 ],
            [ '2023-09-09 16:00', '2023-09-11 18:00', 1 ],
            [ '2023-09-08 23:59', '2023-09-11 16:00', 2 ],

        ];
    }


    public function test_duree_count()
    {

        $saison = Saison::factory()->make([
            'debut_at' => '2023-09-01',
            'fin_at' => '2023-09-09',
        ]);

        foreach ($this->getRerevations() as $key => $reservation) {
            $duree = $saison->getDuree(Carbon::createFromFormat('Y-m-d H:i', $reservation[0]), Carbon::createFromFormat('Y-m-d H:i', $reservation[1]));

            $this->assertEquals($duree, $reservation[2], ($key + 1).' : '.$reservation[0].' -> '.$reservation[1]);
        }

    }


    public function test_duree_total_count()
    {

        $saisons[] = Saison::factory()->make([
            'debut_at' => '2023-08-01',
            'fin_at' => '2023-08-31',
        ]);
        $saisons[] = Saison::factory()->make([
            'debut_at' => '2023-09-01',
            'fin_at' => '2023-09-09',
        ]);
        $saisons[] = Saison::factory()->make([
            'debut_at' => '2023-09-10',
            'fin_at' => '2023-09-30',
        ]);


        foreach ($this->getRerevations() as $key => $reservation) {
            $duree = 0;
            foreach ($saisons as $saison) {
                $duree += $saison->getDuree(Carbon::createFromFormat('Y-m-d H:i', $reservation[0]), Carbon::createFromFormat('Y-m-d H:i', $reservation[1]));
            }

            $this->assertEquals($duree, Reservation::calculDuree(Carbon::createFromFormat('Y-m-d H:i', $reservation[0]), Carbon::createFromFormat('Y-m-d H:i', $reservation[1])));
        }


    }
}
