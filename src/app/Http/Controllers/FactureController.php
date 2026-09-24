<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Http\Requests\StoreFacture;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use PixellWeb\Pennylane\app\Actions\IpsumCustomerAction;
use PixellWeb\Pennylane\app\Actions\IpsumInvoiceAction;
use Prologue\Alerts\Facades\Alert;
use Str;

class FactureController extends AdminController
{
    protected $acces = 'reservation';

    public function index(Request $request)
    {
        $query = Facture::query()->with('reservation');

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['numero'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $factures = $query->paginate();

        return view('IpsumReservation::facture.index', compact('factures'));
    }

    public function create(Reservation $reservation)
    {
        return view('IpsumReservation::facture.form', compact('reservation'));
    }

    public function store(StoreFacture $request, Reservation $reservation, IpsumCustomerAction $ipsumCustomerAction, IpsumInvoiceAction $ipsumInvoiceAction)
    {

        try {

            $ipsumCustomerAction->syncToProvider($reservation);

            $facture = $ipsumInvoiceAction->syncToProvider($reservation);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        $reservation->paiements()->update([
            'facture_id' => $facture->id,
        ]);

        Alert::success("La facture a bien été générée.")->flash();
        return redirect()->route('admin.reservation.edit', $reservation);
    }

    /*public function edit(Facture $facture)
    {
        return view('IpsumReservation::facture.form', compact('facture'));
    }

    public function update(StoreFacture $request, Facture $facture)
    {
        $facture->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }*/

    public function pdf(IpsumInvoiceAction $ipsumInvoiceAction, Facture $facture)
    {
        return redirect()->away($ipsumInvoiceAction->getUrlPdf($facture));
    }
}
