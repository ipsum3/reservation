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
    public $inspection;
    public $type;
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

        /**
         * ÉTAT DES LIEUX
         */
        $pdfPath = storage_path("app/inspections/etat_des_lieux-{$reservation->id}-{$type->id}.pdf");

        if (!file_exists($pdfPath)) {
            throw new \Exception("Le fichier PDF de l’état des lieux est introuvable pour la réservation #{$reservation->id}");
        }

        $this->file = file_get_contents($pdfPath);
        $this->filePath = $pdfPath;

        /**
         * CONTRAT SIGNÉ
         */
        $contratPath = storage_path("app/contrats/contrat-{$reservation->id}.pdf");

        if (!file_exists($contratPath)) {
            throw new \Exception(
                "Le PDF du contrat est introuvable pour la réservation #{$reservation->id}"
            );
        }

        $this->contratFilePath = $contratPath;
        $this->contratFile = file_get_contents($contratPath);

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
            ->attachData(
                $this->contratFile, 'contrat_location-'.$this->reservation->id.'.pdf',
                ['mime' => 'application/pdf']
            )
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->subject('État des lieux '. ($this->type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initial': 'final') .' – Réservation ' . $this->reservation->reference);

    }
}
