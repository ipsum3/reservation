<?php

namespace Ipsum\Reservation\app\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class PlanningOptimiser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planning:optimiser {--categorie=} {--revert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Association des véhicules aux reservation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if ($this->option('revert')) {
            foreach (cache()->get('reservation_optimisation_sauvegarde') as $categories) {
                foreach ($categories as $reservation) {
                    $resa = Reservation::find($reservation['id']);
                    $resa->vehicule_id = $reservation['vehicule_id'];
                    $resa->save();
                }
            }

            return Command::SUCCESS;
        }


        $query = Reservation::query()
            ->confirmed()
            ->where(function ($query) {
                $query->where('vehicule_blocage', 0)->orWhereNull('vehicule_id');
            })
            ->where('debut_at', '>=', Carbon::now()->addHours(config('settings.reservation.battement_entre_reservations')))
            //->orderByRaw('DATEDIFF(fin_at, debut_at) desc');
            ->orderBy('debut_at');

        if( $this->option('categorie') ) {
            $query->where('categorie_id', $this->option('categorie'));
        }

        $sauvegarde = $query->get()->map(function (Reservation $reservation) {
            return [
                'id' => $reservation->id,
                'vehicule_id' => $reservation->vehicule_id,
                'categorie_id' => $reservation->categorie_id,
                'debut_at' => $reservation->debut_at,
                'fin_at' => $reservation->fin_at
            ];
        })->groupBy('categorie_id');

        cache()->put('reservation_optimisation_sauvegarde',  $sauvegarde);

        $query->update(['vehicule_id' => null]);


        /*$planning = Planning::createByReservations();

        $planning = new Planning($ressources, $reservations, $interventions);
        $planning->optimiser();
        $planning->getTauxOptimisation();
        $resas = $planning->getReservations();*/


        $reservation = $query->first();

        $reservation->save();

        foreach ($reservations as $reservation) {
            $reservation->save();
        }


        return Command::SUCCESS;
    }
}
