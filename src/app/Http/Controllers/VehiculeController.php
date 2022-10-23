<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreVehicule;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Prologue\Alerts\Facades\Alert;

class VehiculeController extends AdminController
{
    protected $acces = 'vehicule';

    public function index(Request $request)
    {
        $query = Vehicule::with(['categorie'])->withCount(['reservations' => function (Builder $query) {
            $query->confirmed()->where('fin_at', '>', Carbon::now());
        }]);


        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->get('categorie_id'));
        }

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['marque_modele', 'immatriculation'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $vehicules = $query->orderBy('immatriculation')->paginate();

        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.vehicule.index', compact('vehicules', 'categories'));
    }

    public function create()
    {
        $vehicule = new Vehicule;

        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.vehicule.form', compact('vehicule', 'categories'));
    }

    public function store(StoreVehicule $request)
    {
        $vehicule = Vehicule::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.vehicule.edit', [$vehicule->id]);
    }

    public function edit(Vehicule $vehicule)
    {
        $vehicule->load(['reservations' => function ($query) {
            $query->confirmed()->orderBy('debut_at', 'asc')->limit('20');
        },
        'interventions' => function ($query) {
            $query->where('fin_at', '>=', Carbon::now())->orderBy('debut_at', 'asc')->limit('20');
        }]);

        $types = Type::get()->pluck('nom', 'id');
        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.vehicule.form', compact('vehicule', 'types', 'categories'));
    }

    public function update(StoreVehicule $request, Vehicule $vehicule)
    {
        $vehicule->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Vehicule $vehicule)
    {
        $vehicule->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.vehicule.index');

    }
}
