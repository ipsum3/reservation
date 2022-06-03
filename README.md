## Installation du package reservation de voiture

``` bash
# install the package
composer require ipsum3/reservation

# Run install
php artisan ipsum:reservation:install

# Optional publish views
php artisan vendor:publish --provider="Ipsum\Reservation\ReservationServiceProvider" --tag=views

```

### Add Reservation seeder to DatabaseSeeder.php file
`$this->call(ReservationTableSeeder::class);`
