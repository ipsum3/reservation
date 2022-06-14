<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StorePrestationBlocage;
use Ipsum\Reservation\app\Models\Prestation\Blocage;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Prologue\Alerts\Facades\Alert;

class PrestationBlocageController extends AdminController
{
    protected $acces = 'tarif';

    public function index(Request $request)
    {
        $query = Blocage::with('prestation');

        if ($request->filled('prestation_id')) {
            $query->where('prestation_id', $request->get('prestation_id'));
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

        $prestations = Prestation::get()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.blocage.index', compact('blocages', 'prestations'));
    }

    public function create()
    {
        $blocage = new Blocage();

        $prestations = Prestation::get()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.blocage.form', compact('blocage', 'prestations'));
    }

    public function store(StorePrestationBlocage $request)
    {
        $blocage = Blocage::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.prestationBlocage.edit', [$blocage->id]);
    }

    public function edit(Blocage $blocage)
    {
        $prestations = Prestation::get()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.blocage.form', compact('blocage', 'prestations'));
    }

    public function update(StorePrestationBlocage $request, Blocage $blocage)
    {
        $blocage->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Blocage $blocage)
    {
        $blocage->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.prestationBlocage.index');

    }
}
