<?php

namespace Ipsum\Reservation\app\Mail;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class Confirmation extends Mailable
{
    use Queueable, SerializesModels;


    public $reservation;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, $email = null)
    {
        $this->reservation = $reservation;
        $this->email = $email ? $email : $this->reservation->email;
        App::setLocale($reservation->locale);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view(config('ipsum.reservation.confirmation.view'))
            ->from($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->cc($this->reservation->lieuDebut->email_reservation_first, config('settings.nom_site'))
            ->subject('Confirmation réservation '.$this->reservation->reference);
    }
}
