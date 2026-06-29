<?php

namespace Ipsum\Reservation\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Models\Tarif\TarifException;


class ReservationCheck extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservation:check {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie qu\'il n\'y a pas de trou entre chaque saison et pas de chevauchement';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->option('type');

        if ($type === 'tranche') {
            $return = $this->tranche();
        } elseif ($type === 'saison') {
            $return = $this->saison();
        }else{
            $return = $this->saison();
            $return = array_merge($this->tranche(), $return);
        }

        foreach ($return as $error){
            $this->error($error);
        }

        return Command::SUCCESS;
    }


    /**
     * Vérifie la continuité et les chevauchements des saisons.
     */
    public function saison()
    {
        $messages = [];
        $today = Carbon::today();

        $saisons = Saison::where('fin_at', '>=', $today)
            ->orderBy('debut_at', 'asc')
            ->get();

        if ($saisons->isEmpty()) {
            $messages[] = "Aucune saison trouvée.";
            return $messages;
        }

        if ($saisons->first()->debut_at->gt(Carbon::now())) {
            $messages[] = "Aucune saison pour la date d'aujourd'hui";
        }

        foreach ($saisons as $key => $saison) {
            if (isset($saisons[$key + 1]) && $saison->fin_at->startOfDay()->addDay()->lt($saisons[$key + 1]->debut_at->startOfDay())) {
                $messages[] = "Il existe des dates sans saisons entre " . $saison->fin_at->format('d/m/Y') . " et " . $saisons[$key + 1]->debut_at->format('d/m/Y');
            }
        }

        $chevauchements = DB::select(DB::raw('
            SELECT t.id,
                   t.debut_at,
                   t.fin_at
            FROM   saisons AS t,
                   saisons AS t2
            WHERE  t.fin_at >= \''.$today.'\'
                    AND  t2.fin_at >= \''.$today.'\'
                    AND t.id <> t2.id
                   AND
                   t.debut_at <= t2.fin_at
                   AND
                   t.fin_at >= t2.debut_at
            GROUP BY t.id,
                     t.debut_at,
                     t.fin_at
        '));

        if (!empty($chevauchements)) {
            $messages[] = "Des chevauchements de saisons existe";
        }

        return $messages;
    }

    // TODO A CORRIGER
    public function tranche(): array
    {
        $messages = [];

        $min = 120;

        $durees = Duree::query()
            ->orderBy('min')
            ->orderByDesc('max')
            ->get();

        if ($durees->isEmpty()) {
            return ['Aucune durée configurée.'];
        }

        $current = $min;

        foreach ($durees as $duree) {

            // trou avant cette tranche
            if ($duree->min > $current) {
                $messages[] = sprintf(
                    'Trou entre %s et %s',
                    duration($current),
                    duration($duree->min - 1)
                );
            }

            $current = max($current, $duree->max + 1);
        }

        return $messages;
    }
}
