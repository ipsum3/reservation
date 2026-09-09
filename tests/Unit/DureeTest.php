<?php
declare(strict_types=1);
namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\TarifException;
use Tests\TestCase;

class DureeTest extends TestCase
{

    use RefreshDatabase;

    public function test_find_applicable()
    {

        foreach ($this->getDurees() as $duree) {
            Duree::factory()->create($duree);
        }


        foreach ($this->getDatas() as $label => $data) {

            $duree = Duree::findApplicable(
                Carbon::createFromFormat('Y-m-d H:i', $data['debut']),
                Carbon::createFromFormat('Y-m-d H:i', $data['fin'])
            );

            $this->assertEquals(
                $data['expected'],
                $duree->nom,
                "Scénario '{$label}' : {$data['debut']} → {$data['fin']}"
            );
        }
    }

    private function getDurees()
    {
        return array(
            array(
                'nom' => '2heures',
                'min' => 2 * 60, // 120
                'max' => 5 * 60, // 300
                'priorite' => 10
            ),
            array(
                'nom' => '1jour',
                'min' => 1,
                'max' => 1 * 60 * 24, // 1 440
            ),
            array(
                'nom' => '6jours',
                'min' => (1 * 60 * 24) + 1, // 1 441
                'max' => 7 * 60 * 24, // 10 080
            ),
            array(
                'nom' => '14jours',
                'min' => (7 * 60 * 24) + 1, // 10 081
                'max' => 15 * 60 * 24,
            ),
            array(
                'nom' => '15jours+',
                'min' => (15 * 60 * 24) + 1,
                'max' => null,
            ),
        );
    }

    protected function getDatas(): array
    {
        return [
            /*'0' => [
                'debut' => '2026-07-01 09:00',
                'fin' => '2026-07-01 09:00',
                'expected' => '1jour',
            ],*/

            '1h59' => [
                'debut' => '2026-07-01 09:00',
                'fin' => '2026-07-01 10:59',
                'expected' => '1jour',
            ],
            '2h00' => [
                'debut' => '2026-07-01 09:00',
                'fin' => '2026-07-01 11:00',
                'expected' => '2heures',
            ],
            '4h59' => [
                'debut' => '2026-07-01 09:00',
                'fin' => '2026-07-01 13:59',
                'expected' => '2heures',
            ],


            '1jour_limits' => [
                'debut' => '2026-10-01 09:00',
                'fin' => '2026-10-02 09:00',
                'expected' => '1jour',
            ],
            '2jours' => [
                'debut' => '2026-10-01 09:00',
                'fin' => '2026-10-02 09:01',
                'expected' => '6jours',
            ],


            '6jours_limits' => [
                'debut' => '2026-10-01 09:00',
                'fin' => '2026-10-08 09:00',
                'expected' => '6jours',
            ],
            '7jours' => [
                'debut' => '2026-10-01 09:00',
                'fin' => '2026-10-08 09:01',
                'expected' => '14jours',
            ],


            '14jours_limits' => [
                'debut' => '2026-10-01 09:00',
                'fin' => '2026-10-16 09:00',
                'expected' => '14jours',
            ],
            '15jours' => [
                'debut' => '2026-10-01 09:00',
                'fin' => '2026-10-16 09:01',
                'expected' => '15jours+',
            ],

        ];
    }
}
