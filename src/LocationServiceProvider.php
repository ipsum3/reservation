<?php

namespace Ipsum\Reservation;

use Illuminate\Support\ServiceProvider;
use Ipsum\Reservation\app\Location\Devis;
use Ipsum\Reservation\app\Location\Location;

class LocationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('location', function($app) {
            return Location::newBySession();
        });
    }

    public function provides()
    {
        return array('location');
    }
}