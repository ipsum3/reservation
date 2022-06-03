<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreReservation;
use Ipsum\Reservation\app\Models\Reservation;
use Ipsum\Reservation\app\Models\Categorie;
use Prologue\Alerts\Facades\Alert;

class ReservationController extends AdminController
{
    protected $acces = 'reservation';

    public function index(Request $request, $type)
    {
        $query = Reservation::with('categorie', 'illustration')->where('type', $type);

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->get('categorie_id'));
        }
        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['titre', 'nom', 'extrait', 'texte'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $reservations = $query->latest()->paginate();

        $categories = Categorie::root()->with('children')->orderBy('order')->get();

        return view('IpsumReservation::reservation.index', compact('reservations', 'type', 'categories'));
    }

    public function create($type)
    {
        $reservation = new Reservation;

        $categories = Categorie::root()->with('children')->orderBy('order')->get();

        return view('IpsumReservation::reservation.form', compact('reservation', 'type', 'categories'));
    }

    public function store(StoreReservation $request, $type)
    {
        $reservation = Reservation::create($request->all());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.reservation.edit', [$type, $reservation->id]);
    }

    public function edit($type, Reservation $reservation)
    {
        $categories = Categorie::root()->with('children')->orderBy('order')->get();

        return view('IpsumReservation::reservation.form', compact('reservation', 'type', 'categories'));
    }

    public function update(StoreReservation $request, $type, Reservation $reservation)
    {
        $reservation->update($request->all());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Reservation $reservation)
    {
        if (!$reservation->is_deletable) {
            return abort(403);
        }

        $type = $reservation->type;
        $reservation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.reservation.index', $type);

    }
}
