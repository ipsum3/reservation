<?php

namespace Ipsum\Reservation\app\Contracts;

use Illuminate\View\View;
use Ipsum\Reservation\app\Models\Reservation\Facture;

interface FactureContract
{

    public function syncToProvider(Facture $facture): void;

    public function emmission(Facture $facture): void;

    public function sendToCustomer(Facture $facture, bool|string|array $emails = null): void;

    public function delete(Facture $facture): void;

    public function getUrlPdf(Facture $facture, $cache = true): string;

    public function iframe(): View;
}