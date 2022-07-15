<?php

namespace Ipsum\Reservation\app\Location\Facades;


class Location extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'location';
    }
}