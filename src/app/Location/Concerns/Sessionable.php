<?php

namespace Ipsum\Reservation\app\Location\Concerns;


use Session;

trait Sessionable
{


    static public function hasSession()
    {
        return Session::has(self::class);
    }

    static public function newBySession()
    {
        if (self::hasSession()) {
            return unserialize(Session::get(self::class));
        } else {
            return new self;
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
