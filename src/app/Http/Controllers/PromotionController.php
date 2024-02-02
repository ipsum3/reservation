<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StorePromotion;
use Ipsum\Reservation\app\Location\Prestation;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Promotion\Promotion;

use Ipsum\Reservation\app\Models\Reservation\Condition;
use Prologue\Alerts\Facades\Alert;

class PromotionController extends AdminController
{
    protected $acces = 'promotion';

    public function index(Request $request)
    {
        $query = Promotion::query();

        if ($request->filled('search')) {
            $query->where(function(Builder $query) use ($request) {
                foreach (['nom', 'texte', 'extrait'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $promotions = $query->orderBy('created_at', 'desc')->paginate();


        return view('IpsumReservation::promotion.index', compact('promotions'));
    }

    public function create()
    {
        $promotion = new Promotion;

        $categories = Categorie::orderBy('nom')->get();
        $lieux = Lieu::orderBy('order')->get();
        $prestations = Prestation::orderBy('order')->get();
        $conditions = Condition::orderBy('order')->get()->pluck('nom', 'id');
        $clients = Client::orderBy('nom')->get()->pluck('email', 'id');

        return view('IpsumReservation::promotion.form', compact('promotion', 'categories', 'lieux', 'conditions', 'prestations', 'clients'));
    }

    public function store(StorePromotion $request)
    {
        $promotion = Promotion::create($request->validated());

        $promotion->categories()->sync($request->categories);
        $promotion->lieuxDebut()->sync($request->lieux_debut);
        $promotion->lieuxFin()->sync($request->lieux_fin);
        $promotion->prestations()->sync($request->prestations);

        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.promotion.edit', [$promotion->id]);
    }

    public function edit(Promotion $promotion)
    {
        $categories = Categorie::orderBy('nom')->get();
        $lieux = Lieu::orderBy('order')->get();
        $prestations = Prestation::orderBy('order')->get();
        $conditions = Condition::orderBy('order')->get()->pluck('nom', 'id');
        $clients = Client::orderBy('nom')->get()->pluck('email', 'id');

        return view('IpsumReservation::promotion.form', compact('promotion', 'categories', 'lieux', 'conditions', 'prestations', 'clients'));
    }

    public function update(StorePromotion $request, Promotion $promotion)
    {
        $promotion->update($request->validated());

        $promotion->categories()->sync($request->categories);
        $promotion->lieuxDebut()->sync($request->lieux_debut);
        $promotion->lieuxFin()->sync($request->lieux_fin);
        $promotion->prestations()->sync($request->prestations);

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.promotion.index');
    }

    public function desactivation(Promotion $promotion)
    {
        $promotion->desactivation_at = Carbon::now()->subDay();
        $promotion->save();

        Alert::warning("L'enregistrement a bien été modifié")->flash();
        return redirect()->back();
    }
}
