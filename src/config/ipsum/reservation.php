<?php

return [

    'categorie' => [
        'custom_fields' => [
            [
                'name' => 'tarif_a_partir',
                'label' => 'A partir de (€)',
                'description' => '',
                'defaut' => '',
                'type' => 'number',
                'rules' => 'nullable|numeric'
            ]
        ]
    ],
    'client' => [
        'model' => \App\Models\User::class,
    ],
    'confirmation' => [
        'logo' => null, // env('APP_URL').'/theme/logos/logo-header.jpg'
        'couleur' => '#bbb'
    ],
    'jours-feries' => [
        // https://www.data.gouv.fr/fr/datasets/5b3cc551c751df4822526c1c/
        //'url' => 'https://etalab.github.io/jours-feries-france-data/json/martinique.json'
        //'url' => 'https://calendrier.api.gouv.fr/jours-feries/metropole.json'
        'url' => 'https://etalab.github.io/jours-feries-france-data/json/metropole.json',
        'zone' => 'métropole',
    ],
    'custom_fields' => [
        [
            'name' => 'vol',
            'label' => 'Numéro de vol',
            'description' => '',
            'defaut' => '',
            'type' => 'input',
            'rules' => ''
        ]
    ],
    'recherche' => [
        'date_format' => 'd/m/Y H:i',
        'jour_format' => 'd/m/Y',
        'pays_defaut_id' => 75
    ]

];
