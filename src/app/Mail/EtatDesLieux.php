<?php

    namespace Ipsum\Reservation\app\Mail;

use App;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Inspection\Checklist;
use Ipsum\Reservation\app\Models\Inspection\Inspection;
use Ipsum\Reservation\app\Models\Inspection\Type;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class EtatDesLieux extends Mailable
{
    use Queueable, SerializesModels;


    public $reservation;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, Type $type, $email = null)
    {
        $this->reservation = $reservation;
        $this->type = $type;
        $inspection = $type->id == Type::FINAL_ID ? $reservation->inspection_finale : $reservation->inspection_initiale;
        $this->inspection = $inspection;
        $this->email = $email ? $email : $this->reservation->email;
        $checklists = Checklist::orderBy('order')->get();
        $pdf = Pdf::loadView(config('ipsum.reservation.etat_des_lieux.view'), compact('inspection', 'reservation', 'type', 'checklists'));
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
        return $this->markdown('IpsumReservation::reservation.emails.etat_des_lieux')
            ->attachData($this->file, 'etat_des_lieux_'.$this->type->nom.'.pdf', [
                'mime' => 'application/pdf',
            ])
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->cc($this->reservation->lieuDebut->email_reservation_first, config('settings.nom_site'))
            ->subject('Etat des lieux réservation ' . $this->reservation->reference);

    }
}
