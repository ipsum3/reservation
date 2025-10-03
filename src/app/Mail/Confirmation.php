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


    public ?Reservation $reservation;
    public ?string $email;
    public bool $has_cc = true;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, $email = null, $has_cc = true)
    {
        $this->reservation = $reservation;
        $this->email = $email ?: $this->reservation->email;
        $this->has_cc = $has_cc;

        App::setLocale($reservation->locale);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->view(config('ipsum.reservation.confirmation.view'))
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->subject('Confirmation réservation '.$this->reservation->reference);

        if ($this->has_cc) {
            $this->cc($this->reservation->lieuDebut->emails_reservation, config('settings.nom_site'));
        }

        return $this;
    }
}
