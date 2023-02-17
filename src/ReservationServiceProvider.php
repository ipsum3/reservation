<?php

namespace Ipsum\Reservation;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Ipsum\Reservation\app\Console\Commands\Install;
use Ipsum\Reservation\app\Console\Commands\JoursFeries;
use Ipsum\Reservation\app\Console\Commands\PlanningOptimiser;
use Ipsum\Reservation\app\Http\Middleware\ReservationConfirmed;
use Ipsum\Reservation\app\Http\Middleware\ReservationTracking;
use Ipsum\Reservation\app\Models\Reservation\Paiement;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Policies\PaiementPolicy;
use Ipsum\Reservation\app\Policies\ReservationPolicy;

class ReservationServiceProvider extends ServiceProvider
{

    protected $commands = [
        Install::class,
        JoursFeries::class,
        PlanningOptimiser::class,
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $policies = [
        Reservation::class => ReservationPolicy::class,
        Paiement::class => PaiementPolicy::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Schema::defaultStringLength(191); // Fix version de mysql

        Route::middleware(['web'])
            ->prefix(config('ipsum.admin.route_prefix'))
            ->group(__DIR__.'/routes/admin.php');
        
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViews();
        //$this->loadTranslationsFrom(__DIR__.'/ressources/lang', 'IpsumReservation');

        $this->publishFiles();

        $this->registerMiddlewareGroup($this->app->router);

        //$this->addPolicies();

        Relation::morphMap([
            \Ipsum\Reservation\app\Models\Prestation\Prestation::class => \Ipsum\Reservation\app\Location\Prestation::class,
            \Ipsum\Reservation\app\Models\Promotion\Promotion::class => \Ipsum\Reservation\app\Location\Promotion::class,
        ]);

        // Merge conf acces pour l'admin
        \Config::set('ipsum.admin.acces', array_merge( config('ipsum.admin.acces'), config('ipsum.reservation.acces') ));
    }


    public function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/ressources/views', 'IpsumReservation');

        Blade::anonymousComponentNamespace('IpsumReservation::components', 'reservation');

    }


    public function addPolicies()
    {
        $this->registerPolicies();
    }



    public function publishFiles()
    {
        $this->publishes([
            __DIR__.'/ressources/views' => resource_path('views/ipsum/reservation'),
        ], 'views');
    
        $this->publishes([
            __DIR__.'/database/seeds/' => database_path('seeders'),
        ], 'seeds');
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/ipsum/reservation.php', 'ipsum.reservation'
        );

        // register the artisan commands
        $this->commands($this->commands);
    }


    public function registerMiddlewareGroup(Router $router)
    {
        $router->aliasMiddleware('adminReservationConfirmed', ReservationConfirmed::class);
        $this->app->booted(function () use($router) {
            $router->pushMiddlewareToGroup('web', ReservationTracking::class);
        });
    }
}
