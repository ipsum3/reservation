<?php

namespace Ipsum\Reservation\app\Mail;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Inspection\Inspection;
use Ipsum\Reservation\app\Models\Inspection\Type;
use Str;

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
     * @throws \Exception
     */
    public function __construct(Inspection $inspection, $email = null)
    {
        $this->reservation = $inspection->reservation;
        $this->inspection = $inspection;
        $this->email = $email ?: $this->reservation->email;

        /**
         * ÉTAT DES LIEUX
         */
        $pdfPath = storage_path("app/inspections/etat_des_lieux-{$inspection->id}.pdf");

        if (!file_exists($pdfPath)) {
            throw new \Exception("Le fichier PDF de l’état des lieux est introuvable pour la réservation #{$inspection->reservation->id}");
        }

        $this->file = file_get_contents($pdfPath);
        $this->filePath = $pdfPath;

        /**
         * CONTRAT SIGNÉ
         */
        $contratPath = storage_path("app/contrats/contrat-{$inspection->reservation->contrat}.pdf");

        if (!file_exists($contratPath)) {
            throw new \Exception(
                "Le PDF du contrat est introuvable pour la réservation #{$inspection->reservation->id}"
            );
        }

        $this->contratFilePath = $contratPath;
        $this->contratFile = file_get_contents($contratPath);

        App::setLocale($inspection->reservation->locale);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('IpsumReservation::reservation.emails.etat_des_lieux')
            ->attachData($this->file, 'etat-des-lieux-'.$this->inspection->reservation->id.'-'.Str::slug($this->inspection->type->nom).'.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData(
                $this->contratFile, 'contrat-'.$this->reservation->contrat.'.pdf',
                ['mime' => 'application/pdf']
            )
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->reservation->lieuDebut->email_first, config('settings.nom_site'))
            ->to($this->email, $this->reservation->prenom.' '.$this->reservation->nom)
            ->subject('État des lieux '. strtolower($this->inspection->type->nom) .' – Réservation ' . $this->reservation->reference);

    }
}
