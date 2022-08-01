<?php

namespace Ipsum\Reservation\app\Models\Reservation;


use App\Lieu\Lieu;
use Ipsum\Reservation\app\Classes\Carbon;

trait ReservationValidatorTrait {


    public function validateDateGreaterThan($attribute, $value, $parameters, $validator)
    {
        $format = $parameters[1];
        try {
            $date_min = Carbon::createFromFormat($format, array_get($validator->getData(), $parameters[0]));
            return Carbon::createFromFormat($format, $value)->gte($date_min);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    protected function replaceDateGreaterThan($message, $attribute, $rule, $parameters)
    {
        return str_replace(':date_min', $parameters[0], $message);
    }


    public function validateDateMin($attribute, $value, $parameters, $validator)
    {
        $format = $parameters[1];
        try {
            $date_min = Carbon::createFromFormat($format, $parameters[0]);
            return Carbon::createFromFormat($format, $value)->gt($date_min);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /*protected function replaceDateMin($message, $attribute, $rule, $parameters)
    {
        return str_replace(':date_min', $parameters[0], $message);
    }*/


    public function validateDateMax($attribute, $value, $parameters, $validator)
    {
        $format = $parameters[1];
        try {
            $date_min = Carbon::createFromFormat($format, $parameters[0]);
            return Carbon::createFromFormat($format, $value)->lte($date_min);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    protected function replaceDateMax($message, $attribute, $rule, $parameters)
    {
        return str_replace(':date_max', $parameters[0], $message);
    }


    public function validateDureeMin($attribute, $value, $parameters, $validator)
    {
        $format = $parameters[2];
        try {
            $date_debut = Carbon::createFromFormat($format, array_get($validator->getData(), $parameters[1]));
            $date_fin = Carbon::createFromFormat($format, $value);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $duree_min = $parameters[0];
        return Reservation::nbJours($date_debut, $date_fin) >= $duree_min;
    }

    protected function replaceDureeMin($message, $attribute, $rule, $parameters)
    {
        $duree_min = $parameters[0];
        return str_replace(':duree_min', $duree_min, $message);
    }


    public function validateOuvert($attribute, $value, $parameters, $validator)
    {
        $lieu = Lieu::find(array_get($validator->getData(), $parameters[0]));

        if(!$lieu) {
            return false;
        }

        try {
            $date = Carbon::createFromFormat($parameters[1], $value);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return $lieu->isOuvert($date);
    }

    protected function replaceOuvert($message, $attribute, $rule, $parameters)
    {
        $message = str_replace(':date', \Input::get($attribute), $message);
        return str_replace(':lieu', $parameters[2], $message);
    }

    public function validateOuvertHoraire($attribute, $value, $parameters, $validator)
    {
        $lieu = Lieu::find(array_get($validator->getData(), $parameters[0]));

        if(!$lieu) {
            return false;
        }

        try {
            $date = Carbon::createFromFormat($parameters[1], $value);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return $lieu->isOuvertHoraire($date);
    }

    protected function replaceOuvertHoraire($message, $attribute, $rule, $parameters)
    {
        try {
            $date = Carbon::createFromFormat($parameters[1], array_get($this->getData(), $attribute));
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        $lieu = Lieu::find(array_get($this->getData(), $parameters[0]));
        $horaires = "";
        if($lieu) {
            $creneaux = $lieu->creneauxHorairesToString($date);
            $horaires = $creneaux ? _("Elle est ouverte ce jour là ").$creneaux.'.' : _("Veuillez choisir une autre date.");

        }

        $message = str_replace(':date', $date->format('d/m/Y'), $message);
        $message = str_replace(':heure', $date->format('H\hi'), $message);
        $message = str_replace(':horaires', $horaires, $message);
        return str_replace(':lieu', $parameters[2], $message);
    }

    public function validateZone($attribute, $value, $parameters, $validator)
    {
        $lieu_debut = Lieu::find($value);
        $lieu_fin = Lieu::find(array_get($validator->getData(), $parameters[0]));

        return $lieu_debut->zone_id === $lieu_fin->zone_id;
    }
}
