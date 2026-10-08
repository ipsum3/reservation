<?php
namespace Ipsum\Reservation\app\View\Components;

use Illuminate\View\Component;
use Ipsum\Reservation\app\Contracts\FactureContract;

class Facture extends Component
{

    public ?string $urlPdf;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(private FactureContract $service, \Ipsum\Reservation\app\Models\Reservation\Facture $facture)
    {
        try {
            $this->urlPdf = $this->service->getUrlPdf($facture, false);
        } catch (\Exception $exception) {
            $this->urlPdf = null;
        }
    }


    public function render()
    {
        return $this->service->iframe();
    }
}
