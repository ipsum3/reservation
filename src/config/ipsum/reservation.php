<?php

return [

    'categorie' => [
        'custom_fields' => [
            /*[
                'name' => 'tarif_a_partir',
                'label' => 'A partir de (€)',
                'description' => '',
                'defaut' => '',
                'type' => 'number',
                'rules' => 'nullable|numeric'
            ]*/
        ],
        'translatable_attributes_adds' => []
    ],
    'lieu' => [
        'custom_fields' => [
            /*[
                'name' => 'email_debut',
                'label' => 'Email prise de véhicule',
                'description' => '',
                'defaut' => '',
                'type' => 'html-simple',
                'rules' => 'nullable'
            ],
            [
                'name' => 'email_fin',
                'label' => 'Email retour de véhicule',
                'description' => '',
                'defaut' => '',
                'type' => 'html-simple',
                'rules' => 'nullable'
            ]*/
        ],
        'translatable_attributes_adds' => []
    ],
    'prestation' => [
        'custom_fields' => [],
        'translatable_attributes_adds' => []
    ],
    'saison' => [
        'custom_fields' => []
    ],
    'client' => [
        'custom_fields' => [ ]
    ],
    'confirmation' => [
        'logo' => null, // env('APP_URL').'/theme/logos/logo-header.jpg'
        'couleur' => '#bbb',
        'view' => 'IpsumReservation::reservation.emails.confirmation',
    ],
    'contrat' => [
        'logo' => null, // env('APP_URL').'/theme/logos/logo-header.jpg'
        'couleur' => '#bbb',
        'cgl_nom' => null,
        'view' => 'IpsumReservation::reservation.contrat',
    ],
    'devis' => [
        'logo' => null, // env('APP_URL').'/theme/logos/logo-header.jpg'
        'couleur' => '#bbb',
        'view' => 'IpsumReservation::devis.devis',
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
    'translatable_attributes_adds' => [],
    'recherche' => [
        'date_format' => 'd/m/Y H:i',
        'jour_format' => 'd/m/Y',
        'pays_defaut_id' => 75
    ],
    'tarif' => [
        // Attention aux promotions avec une condition de condition de paiement
        'has_multiple_grille_by_condition' => true,
    ],

    'check_vehicules_disponible' => false,

    'numero_longueur' => 6,

];
