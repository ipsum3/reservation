<?php

namespace Ipsum\Reservation\app\Panier;


class Location extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'panier';
    }
}