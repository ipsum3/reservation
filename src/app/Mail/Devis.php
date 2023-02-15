<?php

namespace Ipsum\Reservation\app\Mail;

use App;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class Devis extends Mailable
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
        $pdf = Pdf::loadView(config('ipsum.reservation.devis.view'), compact('reservation'));
        $pdf->render();
        $this->file = $pdf->output();
        App::setLocale($reservation->locale);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('IpsumReservation::reservation.emails.devis')
            ->attachData($this->file, 'Devis.pdf', [
                'mime' => 'application/pdf',
            ])
            ->from($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->subject('Devis réservation ' . $this->reservation->reference);
    }
}
