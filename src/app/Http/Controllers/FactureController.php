<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Contracts\FactureContract;
use Ipsum\Reservation\app\Enum\FactureEtat;
use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Http\Requests\BrouillonFacture;
use Ipsum\Reservation\app\Http\Requests\CreateFacture;
use Ipsum\Reservation\app\Http\Requests\StoreFacture;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Paiement;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Reservation\Type;
use PixellWeb\Pennylane\app\Actions\IpsumCustomerAction;
use PixellWeb\Pennylane\app\Actions\IpsumInvoiceAction;
use Prologue\Alerts\Facades\Alert;
use Str;

class FactureController extends AdminController
{
    protected $acces = 'reservation';

    public function __construct(private FactureContract $service)
    {
        parent::__construct();
    }


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

    public function create(CreateFacture $request, Reservation $reservation)
    {
        $moyens = Moyen::all();
        $types = Type::all();
        $prestations = Prestation::orderBy('order')->get();

        $facture = new Facture();
        $facture->echeance_at = now()->addMonth();
        $facture->client()->associate($reservation->entreprise_id ?? $reservation->client_id);

        return view('IpsumReservation::facture.brouillon', compact('facture', 'reservation','moyens', 'types', 'prestations'));
    }

    public function storeBrouillon(BrouillonFacture $request, Reservation $reservation)
    {
        $factureType = $reservation->factureLocation ? FactureType::ADDITIONNELLE : FactureType::LOCATION;

        $facture = Facture::create($request->validated() + [
                'reservation_id' => $reservation->id,
                'client_id' => $reservation->entreprise_id ?? $reservation->client_id,
                'type' => $factureType,
                'etat' => FactureEtat::BROUILLON,
            ]);
        $facture->produits()->sync($request->validated('produits'));
        $facture->updateTotal()->save();


        if ($request->has('paiements')) {
            foreach ($request->validated('paiements') as $paiement) {
                Paiement::updateOrCreate([
                    'id' => $paiement['id'],
                ], $paiement + ['facture_id' => $facture->id]);
            }
        }
        $facture->updateMontantPaye()->save();

        try {
            $this->service->syncToProvider($facture);
        } catch (\Exception $e) {
            Alert::error($e->getMessage())->flash();
            return redirect()->route('admin.facture.edit.brouillon', $facture);
        }

        Alert::success("Le brouillon a été enregistré.")->flash();
        return redirect()->route('admin.facture.edit.brouillon', $facture);
    }

    public function editBrouillon(Facture $facture)
    {
        $moyens = Moyen::all();
        $types = Type::all();
        $prestations = Prestation::orderBy('order')->get();

        $reservation = $facture->reservation;

        return view('IpsumReservation::facture.brouillon', compact('facture', 'reservation', 'moyens', 'types', 'prestations'));
    }

    public function updateBrouillon(BrouillonFacture $request, Facture $facture)
    {
        $facture->update($request->validated() + [
                'client_id' => $facture->reservation->entreprise_id ?? $facture->reservation->client_id,
            ]);
        $facture->produits()->sync($request->validated('produits'));
        $facture->updateTotal()->save();


        if ($request->has('paiements')) {
            foreach ($request->validated('paiements') as $paiement) {
                Paiement::updateOrCreate([
                    'id' => $paiement['id'],
                ], $paiement + ['facture_id' => $facture->id]);
            }
        }
        $facture->paiements()->whereDoesntHave('reservation')->whereNotIn('id', collect($request->validated('paiements'))->pluck('id'))->delete();
        $facture->updateMontantPaye()->save();

        try {
            $this->service->syncToProvider($facture);
        } catch (\Exception $e) {
            Alert::error($e->getMessage())->flash();
            return back();
        }

        if ($request->has('brouillon')) {
            Alert::success("Le brouillon a été enregistré.")->flash();
            return redirect()->route('admin.facture.edit.brouillon', $facture);
        }

        try {
            $this->service->emmission($facture);
        } catch (\Exception $e) {
            Alert::error($e->getMessage())->flash();
            return back();
        }

        Alert::success("La facture a bien été générée. Elle sera envoyée au client dans quelques instants.")->flash();
        return redirect()->route('admin.reservation.edit', $facture->reservation);

    }


    public function edit(Facture $facture)
    {
        $reservation = $facture->reservation;
        $moyens = Moyen::all();
        $types = Type::all();

        $url_pdf = $this->service->getUrlPdf($facture, false);

        return view('IpsumReservation::facture.update', compact('facture', 'reservation', 'moyens', 'types', 'url_pdf'));
    }

    public function update(StoreFacture $request, Facture $facture)
    {

        if ($request->has('paiements')) {
            foreach ($request->validated('paiements') as $paiement) {
                Paiement::updateOrCreate([
                    'id' => $paiement['id'],
                ], $paiement + ['facture_id' => $facture->id]);
            }
        }
        $facture->paiements()->whereDoesntHave('reservation')->whereNotIn('id', collect($request->validated('paiements'))->pluck('id'))->delete();
        $facture->updateMontantPaye()->save();

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function pdf(Facture $facture)
    {
        return redirect()->away($this->service->getUrlPdf($facture));
    }

    public function destroy(Facture $facture)
    {
        $this->service->delete($facture);
        $facture->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.reservation.index');

    }
}
