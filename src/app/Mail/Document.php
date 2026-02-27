<?php

namespace Ipsum\Reservation\app\Mail;

use App;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class Document extends Mailable
{
    use Queueable, SerializesModels;


    public $reservation;
    public $email;
    public $objet;
    public $message;
    public $document_file;
    public $document_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, string $document_path, string $document_name, string $objet, string $message, $email = null)
    {
        $this->reservation = $reservation;
        $this->document_file = file_get_contents($document_path);
        $this->document_name = $document_name;
        $this->objet = $objet;
        $this->message = $message;
        $this->email = $email ?: $this->reservation->email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('IpsumReservation::reservation.emails.document')
            ->attachData($this->document_file, $this->document_name, [
                'mime' => 'application/pdf',
            ])
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->subject($this->objet);

    }
}
