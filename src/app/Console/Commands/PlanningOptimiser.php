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
    protected $signature = 'planning:optimiser';

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

        $query = Reservation::query()
            ->confirmed()
            ->where(function ($query) {
                $query->where('vehicule_blocage', 0)->orWhereNull('vehicule_id');
            })
            ->where('debut_at', '>=', Carbon::now()->addHours(config('settings.reservation.battement_entre_reservations')))
            ->orderByRaw('DATEDIFF(fin_at, debut_at) desc');

        $query->update(['vehicule_id' => null]);

        $reservations = $query->get();

        foreach ($reservations as $reservation) {
            $reservation->save();
        }


        return Command::SUCCESS;
    }
}
