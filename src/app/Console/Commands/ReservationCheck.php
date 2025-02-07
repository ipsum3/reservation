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


    public function tranche()
    {
        $messages = [];

        $dureesParType = Duree::orderBy('min', 'asc')
            ->get()
            ->groupBy('is_special');

        foreach ($dureesParType as $isSpecial => $durees) {
            // Vérifier les trous entre les tranches
            foreach ($durees as $key => $duree) {
                if (isset($durees[$key + 1])
                    && $durees[$key + 1]->max !== null
                    && $duree->max + 1 != $durees[$key + 1]->min) {
                    $messages[] = "Il existe des durées sans tranche pour " . ($isSpecial ? "les week-ends" : "les jours normaux") . ".";
                }
            }
        }

        $chevauchements = DB::select(DB::raw('
            SELECT t.id,
                   t.min,
                   t.max
            FROM   durees AS t,
                   durees AS t2
            WHERE  t.is_special = 0
                   AND t2.is_special = 0
                   AND t.id <> t2.id
                   AND
                   t.min <= t2.max
                   AND
                   t.max >= t2.min
            GROUP BY t.id,
                     t.min,
                     t.max
        '));

        if (!empty($chevauchements)) {
            $messages[] = "Des chevauchements de tranche existe";
        }

        $chevauchements = DB::select(DB::raw('
            SELECT t.id,
                   t.min,
                   t.max
            FROM   durees AS t,
                   durees AS t2
            WHERE  t.is_special = 1
                   AND t2.is_special = 1
                   AND t.id <> t2.id
                   AND
                   t.min <= t2.max
                   AND
                   t.max >= t2.min
            GROUP BY t.id,
                     t.min,
                     t.max
        '));

        if (!empty($chevauchements)) {
            $messages[] = "Des chevauchements de tranche existe";
        }

        return $messages;
    }
}
