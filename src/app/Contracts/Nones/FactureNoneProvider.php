<?php

namespace Ipsum\Reservation\app\Contracts\Nones;


use Illuminate\View\View;
use Ipsum\Reservation\app\Contracts\FactureContract;
use Ipsum\Reservation\app\Models\Reservation\Facture;


class FactureNoneProvider implements FactureContract
{


    public function syncToProvider(Facture $facture, bool $brouillon = true): void
    {

    }

    public function emmission(Facture $facture): void
    {

    }

    public function sendToCustomer(Facture $facture, bool|string $email = null): void
    {

    }

    public function delete(Facture $facture): void
    {

    }

    public function getUrlPdf(Facture $facture, $cache = true): string
    {
        return '';
    }

    public function iframe(): string|View
    {
        return '<iframe src="" style="width:100%; height: 700px;" frameborder="0"></iframe>';
    }
}