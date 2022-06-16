<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreAdminReservation;
use Ipsum\Reservation\app\Mail\Confirmation;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Prologue\Alerts\Facades\Alert;

class ReservationController extends AdminController
{
    protected $acces = 'reservation';

    public function index(Request $request)
    {
        $query = Reservation::with('etat', 'modalite', 'client');

        if ($request->filled('etat_id')) {
            $query->where('etat_id', $request->get('etat_id'));
        }
        if ($request->filled('modalite_paiement_id')) {
            $query->where('modalite_paiement_id', $request->get('modalite_paiement_id'));
        }
        if ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->get('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->get('date_fin'));
        }
        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['reference', 'nom', 'prenom', 'email', 'telephone'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $reservations = $query->latest()->paginate();

        $etats = Etat::all()->pluck('nom', 'id');
        $modalites = Modalite::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.index', compact('reservations', 'etats', 'modalites'));
    }

    public function create()
    {
        $reservation = new Reservation;

        $etats = Etat::all()->pluck('nom', 'id');
        $modalites = Modalite::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::all()->pluck('nom', 'id');
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'modalites', 'pays', 'categories', 'lieux'));
    }

    public function store(StoreAdminReservation $request)
    {
        $reservation = Reservation::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.reservation.edit', $reservation);
    }

    public function edit(Reservation $reservation)
    {
        $etats = Etat::all()->pluck('nom', 'id');
        $modalites = Modalite::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::all()->pluck('nom', 'id');
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'modalites', 'pays', 'categories', 'lieux'));
    }

    public function update(StoreAdminReservation $request, Reservation $reservation)
    {
        $reservation->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.reservation.index');

    }

    public function confirmation(Reservation $reservation)
    {
        return view('IpsumReservation::reservation.emails.confirmation', compact('reservation'));
    }

    public function confirmationSend(Reservation $reservation)
    {
        try {

            Mail::send(new Confirmation($reservation));
            Alert::success("L'email de confirmation a bien été envoyé")->flash();

        } catch (\Exception $exception) {
            Alert::error("Impossible d'envoyer l'email")->flash();
        }

        return back();
    }
}
