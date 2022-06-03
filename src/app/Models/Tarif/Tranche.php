<?php

namespace Ipsum\Reservation\app\Models\Tarif;


trait Tranche
{

    public static function findByNbJours($nb_jours)
    {
        $duree = self::where(function ($q) use ($nb_jours) {
            $q->where('min', '<=', $nb_jours)->where('max', '>=', $nb_jours);
        })->orWhere(function ($q) use ($nb_jours) {
            $q->where('min', '<=', $nb_jours)->whereNull('max');
        })->first();

        if ($duree === null) {
            throw new TarifException('Aucune tranche pour '.$nb_jours.' jours.');
        }

        return $duree;
    }

    public static function check()
    {
        $messages = null;

        $durees = self::orderBy('min', 'asc')->get();

        if ($durees->first()->min != 1) {
            $messages[] = "La première tranche doit commencer à 1";
        }

        if ($durees->last()->max !== null) {
            $messages[] = "La dernière tranche doit avoir le champ max vide";
        }

        foreach ($durees as $key => $duree) {
            if (isset($durees[$key + 1]) and $durees[$key + 1]->max !== null and $duree->max + 1 != $durees[$key + 1]->min) {
                $messages[] = "Il existe des durées sans tranche.";
            }
        }

        $chevauchements = \DB::select(\DB::raw('
            SELECT t.id,
                   t.min,
                   t.max
            FROM   duree AS t,
                   duree AS t2
            WHERE  t.id <> t2.id
                   AND
                   t.min <= t2.max
                   AND
                   t.max >= t2.min
            GROUP BY t.id,
                     t.min,
                     t.max
        '));

        if (!empty($chevauchements)) {
            $messages[] = "Des chevauchements de tranche existe";
        }

        if ($messages) {
            throw new TarifException(implode($messages, ' ; '));
        }
    }
}
