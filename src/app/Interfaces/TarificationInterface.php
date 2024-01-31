<?php

namespace Ipsum\Reservation\app\Interfaces;

use Carbon\CarbonInterface;
use Ipsum\Reservation\app\Location\Categorie;
use Ipsum\Reservation\app\Location\Prestation;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

interface TarificationInterface
{
    public static function calculer(Prestation $prestation, int $nb_jours, Categorie $categorie, Lieu $lieu_debut, Lieu $lieu_fin, CarbonInterface $debut_at, CarbonInterface $fin_at);
}