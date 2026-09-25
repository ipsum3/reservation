<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Http\Requests\Caution\PreCreateFacture;
use Ipsum\Reservation\app\Http\Requests\CreateFacture;
use Ipsum\Reservation\app\Http\Requests\StoreFacture;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Reservation\Type;
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

    public function create(Reservation $reservation, IpsumCustomerAction $ipsumCustomerAction, IpsumInvoiceAction $ipsumInvoiceAction)
    {

        $validator = Validator::make($reservation->toArray(), [
            "prenom" => "required",
            "telephone" => "required",
            "adresse" => "required",
            "cp" => "required",
            "ville" => "required",
            "pays_id" => "required",

            "montant_base" => "required",
            "total" => "required",
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $moyens = Moyen::all();
        $types = Type::all();
        $prestations = Prestation::orderBy('order')->get();
        $facture = new Facture();

        return view('IpsumReservation::facture.create', compact('facture', 'reservation','moyens', 'types', 'prestations'));
    }

    public function store(CreateFacture $request, Reservation $reservation, IpsumCustomerAction $ipsumCustomerAction, IpsumInvoiceAction $ipsumInvoiceAction)
    {

        // TODO enregistrement produit

        try {
            $ipsumCustomerAction->syncToProvider($reservation);
            $facture = $ipsumInvoiceAction->syncToProvider($reservation);
        } catch (\Exception $e) {
            Alert::error($e->getMessage())->flash();
            return back();
        }

        if ($request->has('paiements')) {
            $reservation->paiements()->insert(
                $request->validated('paiements')
            );
        }
        $reservation->updateMontantPaye()->save();

        $reservation->paiements()->doesntHave('facture')->update([
            'facture_id' => $facture->id,
        ]);

        Alert::success("La facture a bien été générée.")->flash();
        return redirect()->route('admin.reservation.edit', $reservation);
    }

    public function edit(IpsumInvoiceAction $ipsumInvoiceAction, Facture $facture)
    {
        $reservation = $facture->reservation;
        $moyens = Moyen::all();
        $types = Type::all();

        $url_pdf = Cache::remember('facture-'.$facture->numero, 5 * 60, function () use ($facture, $ipsumInvoiceAction) {
            return $ipsumInvoiceAction->getUrlPdf($facture);
        });

        return view('IpsumReservation::facture.update', compact('facture', 'reservation', 'moyens', 'types', 'url_pdf'));
    }

    public function update(StoreFacture $request, Facture $facture)
    {
        // TODO bug à l'enregistrement facture_id n'est pas bon
        //dd($request->validated('paiements'));
        //dd($request->validated('paiements') + ['reservation_id' => $facture->reservation_id]);
        if ($request->has('paiements')) {
            $facture->paiements()->createMany(
                $request->validated('paiements')
            );
        }
        $facture->reservation->updateMontantPaye()->save();

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function pdf(IpsumInvoiceAction $ipsumInvoiceAction, Facture $facture)
    {
        return redirect()->away($ipsumInvoiceAction->getUrlPdf($facture));
    }
}
