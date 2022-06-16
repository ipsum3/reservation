<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreSaison;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Prologue\Alerts\Facades\Alert;

class SaisonController extends AdminController
{
    protected $acces = 'tarifs';

    public function index(Request $request)
    {
        $query = Saison::query();

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
        $saisons = $query->orderBy('debut_at', 'desc')->paginate();

        return view('IpsumReservation::tarif.saison.index', compact('saisons'));
    }

    public function create()
    {
        $saison = new Saison;

        return view('IpsumReservation::tarif.saison.form', compact('saison'));
    }

    public function store(StoreSaison $request)
    {
        $saison = Saison::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.saison.edit', [$saison->id]);
    }

    public function edit(Saison $saison)
    {
        return view('IpsumReservation::tarif.saison.form', compact('saison'));
    }

    public function update(StoreSaison $request, Saison $saison)
    {
        $saison->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Saison $saison)
    {
        $saison->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.saison.index');

    }

    public function cloner(Saison $saison)
    {
        $saison_clone = $saison->replicateWithTarifs();

        return redirect()->route('admin.saison.edit', [$saison_clone->id]);
    }
}
