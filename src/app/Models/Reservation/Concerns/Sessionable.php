<?php

namespace Ipsum\Reservation\app\Models\Reservation\Concerns;

trait Sessionable
{

    static public function hasSession()
    {
        return Session::has(self::SESSION_ID);
    }

    static public function newBySession()
    {
        if (self::hasSession()) {
            $resa = unserialize(Session::get(self::SESSION_ID));
            $resa->relations = [];  /* Suppression des relations */
            return $resa;
        } else {
            return new self;
        }
    }

    public function saveToSession()
    {
        return Session::put(self::SESSION_ID, serialize($this));
    }

    public function forget()
    {
        Session::forget(self::SESSION_ID);
    }

}
