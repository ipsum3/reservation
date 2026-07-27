<?php

namespace Ipsum\Reservation\app\Mail;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class CautionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    /**
     * Create a new message instance.
     *
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation, string $email = null, string $objet = null)
    {
        $this->reservation = $reservation;
        $this->email = $email ?: $this->reservation->email;
        $this->objet = $objet ?: 'Dépôt de caution - Réservation '.$this->reservation->reference;

        App::setLocale($reservation->locale);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->markdown('IpsumReservation::reservation.emails.caution')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->subject($this->objet);

        return $this;
    }
}