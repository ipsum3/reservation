<?php

namespace Ipsum\Reservation\app\Mail;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Inspection\Inspection;

class NewDommage extends Mailable
{
    use Queueable, SerializesModels;


    public ?Inspection $inspection;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Inspection $inspection)
    {
        $this->inspection = $inspection;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->markdown('IpsumReservation::reservation.emails.new_dommage')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('settings.reservation.email_alerte_dommage'))
            ->subject('Nouveau dommage #'.$this->inspection->id);

        return $this;
    }
}
