<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Core\app\Models\Setting;
use Str;
use Ipsum\Reservation\app\Models\Source\Source;
use Ipsum\Reservation\app\Models\Source\Type;

class SourcesTableSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getSourceTypes() as $source) {
            Type::create($source);
        }

        foreach ($this->getSources() as $source) {
            $source['ref_tracking'] = Str::random(30);
            Source::create($source);
        }

    }

    private function getSourceTypes()
    {
        return array(
            array(
                'nom' => 'Web',
                'icon' => 'globe',
            ),
            array(
                'nom' => 'Direct',
                'icon' => 'home',
            ),
            array(
                'nom' => 'Publicité',
                'icon' => 'bullhorn',
            ),
        );
    }

    private function getSources()
    {
        return array(
            array(
                'nom' => 'Site internet',
                'type_id' => '1',
            ),
            array(
                'nom' => 'Agence',
                'type_id' => '2',
            ),
            array(
                'nom' => 'Appel téléphonique',
                'type_id' => '2',
            ),
            array(
                'nom' => 'Google Ads',
                'type_id' => '3',
            ),
            array(
                'nom' => 'Facebook',
                'type_id' => '3',
            ),
        );
    }
}
