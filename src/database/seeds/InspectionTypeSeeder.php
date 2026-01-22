<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Inspection\Type;


class InspectionTypeSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getTypes() as $type) {
            Type::create([
                'nom' => $type,
            ]);
        }
    }

    private function getTypes()
    {
        return [
            'Initial',
            'Final'
        ];
    }

}
