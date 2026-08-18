<?php

namespace Ipsum\Reservation\app\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Ipsum\Reservation\app\Mail\CautionRequestMail;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Services\GandoService;
use Ipsum\Reservation\app\Services\SwiklyService;

class CautionSend extends Command
{
    /**
     * @var string
     */
    protected $signature = 'caution:send';

    /**
     * @var string
     */
    protected $description = 'Envoie les demandes de caution aux réservations concernées';

    public function handle(): int
    {
        $days = config('ipsum.reservation.caution_days_before_departure');

        $start = Carbon::today(); // TODO VOIR POUR LES RESA EFFECTUER LE JOUR MEME
        $end   = Carbon::today()->addDays($days)->endOfDay();

        // Sélectionne les réservations confirmées prévues dans la fenêtre de temps
        // qui n'ont PAS ENCORE de paiement de caution enregistré
        Reservation::confirmed()
            ->whereBetween('debut_at', [$start, $end])
            ->wherenull('caution_url')
            ->whereNotNull('caution')
            ->chunkById(100, function ($reservations) {

                foreach ($reservations as $reservation) {
                    try {
                        switch (config('ipsum.reservation.caution_provider')) {
                            case 'swikly':
                                $service = new SwiklyService();
                                $reservation = $service->createDepositLink($reservation);
                                break;
                            case 'gando':
                                $service = new GandoService();
                                $reservation = $service->createDepositLink($reservation);
                                break;
                            default:
                                throw new \Exception('Provider not found');
                        }

                        if ($reservation->email) {
                             Mail::send(new CautionRequestMail($reservation));

                            $reservation->update([
                                'caution_send_at' => now(),
                            ]);

                            $this->info("Demande de caution créée et envoyée pour la réservation #{$reservation->reference} (ID: {$reservation->id}).");
                        } else {
                            $this->warn("Réservation #{$reservation->reference} (ID: {$reservation->id}) : Aucun email disponible pour l'envoi.");
                        }

                    } catch (\Throwable $e) {
                        $this->error("Erreur pour la réservation #{$reservation->reference} (ID: {$reservation->id}) : " . $e->getMessage());
                    }
                }

            });

        return self::SUCCESS;
    }
}