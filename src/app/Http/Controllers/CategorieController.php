<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreCategorie;
use Ipsum\Reservation\app\Models\Categorie\Carrosserie;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Motorisation;
use Ipsum\Reservation\app\Models\Categorie\Transmission;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Prologue\Alerts\Facades\Alert;

class CategorieController extends AdminController
{
    protected $acces = 'categorie';

    public function index(Request $request)
    {
        $query = Categorie::with('illustration')->withCount(['blocages' => function (Builder $query) {
            $query->where('fin_at', '>', Carbon::now());
        }]);

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->get('type_id'));
        }

        if ($request->filled('carrosserie_id')) {
            $query->where('carrosserie_id', $request->get('carrosserie_id'));
        }

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['nom', 'modeles', 'description'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $categories = $query->orderBy('nom')->paginate();

        $types = Type::get()->pluck('nom', 'id');
        $carrosseries = Carrosserie::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.index', compact('categories', 'types', 'carrosseries'));
    }

    public function create()
    {
        $categorie = new Categorie;

        $types = Type::get()->pluck('nom', 'id');
        $motorisations = Motorisation::get()->pluck('nom', 'id');
        $transmissions = Transmission::get()->pluck('nom', 'id');
        $carrosseries = Carrosserie::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.form', compact('categorie', 'types', 'motorisations', 'transmissions', 'carrosseries'));
    }

    public function store(StoreCategorie $request)
    {
        $categorie = Categorie::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.categorie.edit', [$categorie->id]);
    }

    public function edit(Categorie $categorie)
    {
        $types = Type::get()->pluck('nom', 'id');
        $motorisations = Motorisation::get()->pluck('nom', 'id');
        $transmissions = Transmission::get()->pluck('nom', 'id');
        $carrosseries = Carrosserie::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.form', compact('categorie', 'types', 'motorisations', 'transmissions', 'carrosseries'));
    }

    public function update(StoreCategorie $request, Categorie $categorie)
    {
        $categorie->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Categorie $categorie)
    {
        $categorie->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.categorie.index');

    }
}
