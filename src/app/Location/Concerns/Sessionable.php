<?php

namespace Ipsum\Reservation\app\Location\Concerns;


use Ipsum\Reservation\app\Location\Location;
use Session;

trait Sessionable
{


    static public function hasSession()
    {
        return Session::has(self::class);
    }

    static public function newBySession(): Location
    {
        if (self::hasSession()) {
            $resa = unserialize(Session::get(self::class));
            $resa->sessions = null;
            $resa->duree = null;
            return $resa;
        } else {
            return new Location();
        }
    }

    public function saveToSession()
    {
        Session::put(self::class, serialize($this));
    }

    public function forget()
    {
        Session::forget(self::class);
    }
}
