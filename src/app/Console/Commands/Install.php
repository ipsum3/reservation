<?php

namespace Ipsum\Reservation\app\Console\Commands;

use Ipsum\Core\app\Console\Commands\Command;
use Ipsum\Reservation\app\Models\Categorie\Categorie;


class Install extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ipsum:reservation:install
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Require dev packages and publish files for Ipsum\Reservation to work';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->progressBar = $this->output->createProgressBar(2);
        $this->progressBar->start();
        $this->info(" Ipsum\Reservation installation started. Please wait...");
        $this->progressBar->advance();

        $this->line(' Publishing configs, langs, views and Ipsum Assets files');
        $this->executeProcess('php artisan vendor:publish --provider="Ipsum\Reservation\ReservationServiceProvider" --tag=install');

        $this->line(" Generating users table (using Laravel's default migrations)");
        $this->executeProcess('php artisan migrate');

        $this->line(" Seeding reservation tables");
        if (!Categorie::count()) {
            $this->executeProcess('php artisan db:seed --class=Ipsum\Reservation\database\seeds\DatabaseSeeder');
        } else {
            $this->info(" Seed already done");
        }

        $this->progressBar->finish();
        $this->info(" Ipsum\Reservation installation finished.");
    }
}
