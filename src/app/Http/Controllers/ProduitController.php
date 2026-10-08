<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Ipsum\Reservation\app\Classes\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StorePrestation;
use Ipsum\Reservation\app\Http\Requests\StoreProduit;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Tarification;
use Ipsum\Reservation\app\Models\Prestation\Taxe;
use Ipsum\Reservation\app\Models\Prestation\Type;
use Ipsum\Reservation\app\Models\Categorie\Type as CategorieType;
use Prologue\Alerts\Facades\Alert;

class ProduitController extends AdminController
{
    protected $acces = 'tarifs';

    public function index(Request $request)
    {
        $query = Prestation::with(['type', 'taxe']);

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->get('type_id'));
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
        $produits = $query->orderBy('order')->paginate();

        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.produit.index', compact('produits', 'types'));
    }

    public function create()
    {
        $produit = new Prestation;
        $produit->taxe_id = Taxe::where('default', 1)->first()->id;

        $types = Type::all()->pluck('nom', 'id');
        $taxes = Taxe::orderBy('taux')->get()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.produit.form', compact('produit', 'types', 'taxes'));
    }

    public function store(StoreProduit $request)
    {
        $produit = Prestation::create($request->validated() + ['tarification_id' => Tarification::FORFAIT_ID]);

        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.produit.edit', [$produit->id]);
    }

    public function edit(Prestation $produit)
    {
        $types = Type::all()->pluck('nom', 'id');
        $taxes = Taxe::orderBy('taux')->get()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.produit.form', compact('produit', 'types', 'taxes'));
    }

    public function update(StoreProduit $request, Prestation $produit)
    {
        $produit->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Prestation $produit)
    {
        $produit->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.produit.index');

    }
}
