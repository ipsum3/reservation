<?php

namespace Ipsum\Reservation\app\Console\Commands;

use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Lieu\Ferie;
use Illuminate\Console\Command;


class JoursFeries extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ipsum:reservation:joursferies  {type=import} {--nom=} {--jour=} {--mois=} {--annee-fin=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importe les jours fériés des 2 prochaines années';

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
        $type = $this->argument('type');

        if ($type === 'generate') {
            $this->generate();
        } else {
            $this->import();
        }

        return Command::SUCCESS;
    }


    protected function import()
    {
        $this->info("Import des jours fériés");

        $jours_existant = Ferie::all()->map(function ($value) {
            return $value->jour_at->format('Y-m-d');
        })->toArray();

        $json = file_get_contents(config('ipsum.reservation.jours-feries.url'));
        $feries = json_decode($json, TRUE);


        foreach ($feries as $date => $nom) {
            $jour_at = Carbon::create($date);
            if ($jour_at->greaterThan(Carbon::now()) and $jour_at->lessThan(Carbon::now()->addYears(2)) and !in_array($date, $jours_existant)) {
                Ferie::create([
                    'nom' => $nom,
                    'jour_at' => $jour_at,
                ]);
            }
        }


        $this->info("Import des jours fériés terminé.");
    }


    protected function generate()
    {
        $jours_existant = Ferie::all()->map(function ($value) {
            return $value->jour_at->format('Y-m-d');
        })->toArray();

        $annee = Carbon::now()->year;

        while ($annee <= $this->option('annee-fin')) {

            $date = Carbon::create($annee, $this->option('mois'), $this->option('jour'));

            if (!in_array($date->format('Y-m-d'), $jours_existant)) {
                Ferie::create([
                    'nom' => $this->option('nom'),
                    'jour_at' => $date,
                ]);
            }

            $annee++;
        }

    }
}
