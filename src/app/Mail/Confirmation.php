<?php

namespace Ipsum\Reservation\app\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class Confirmation extends Mailable
{
    use Queueable, SerializesModels;


    public $reservation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('IpsumReservation::reservation.emails.confirmation')
            ->from($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->reservation->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->cc($this->reservation->lieuDebut->email_reservation_first, config('settings.nom_site'))
            ->subject('Confirmation réservation '.$this->reservation->reference);
    }
}
