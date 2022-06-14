<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreCategorieBlocage;
use Ipsum\Reservation\app\Models\Categorie\Blocage;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Prologue\Alerts\Facades\Alert;

class CategorieBlocageController extends AdminController
{
    protected $acces = 'categorie';

    public function index(Request $request)
    {
        $query = Blocage::with('categorie');

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->get('categorie_id'));
        }

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['nom'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $blocages = $query->orderBy('debut_at', 'desc')->paginate();

        $categories = Categorie::get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.blocage.index', compact('blocages', 'categories'));
    }

    public function create()
    {
        $blocage = new Blocage;

        $categories = Categorie::get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.blocage.form', compact('blocage', 'categories'));
    }

    public function store(StoreCategorieBlocage $request)
    {
        $blocage = Blocage::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.categorieBlocage.edit', [$blocage->id]);
    }

    public function edit(Blocage $blocage)
    {
        $categories = Categorie::get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.blocage.form', compact('blocage', 'categories'));
    }

    public function update(StoreCategorieBlocage $request, Blocage $blocage)
    {
        $blocage->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Blocage $blocage)
    {
        $blocage->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.categorieBlocage.index');

    }
}
