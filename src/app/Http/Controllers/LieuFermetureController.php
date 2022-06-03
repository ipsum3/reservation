<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreLieuFermeture;
use Ipsum\Reservation\app\Models\Lieu\Fermeture;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Prologue\Alerts\Facades\Alert;

class LieuFermetureController extends AdminController
{
    protected $acces = 'lieu';

    public function index(Request $request)
    {
        $query = Fermeture::with('lieu');

        if ($request->filled('lieu_id')) {
            $query->where('lieu_id', $request->get('lieu_id'));
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
        $fermetures = $query->orderBy('debut_at', 'desc')->paginate();

        $lieux = Lieu::get()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.fermeture.index', compact('fermetures', 'lieux'));
    }

    public function create()
    {
        $fermeture = new Fermeture;

        $lieux = Lieu::get()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.fermeture.form', compact('fermeture', 'lieux'));
    }

    public function store(StoreLieuFermeture $request)
    {
        $fermeture = Fermeture::create($request->all());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.lieuFermeture.edit', [$fermeture->id]);
    }

    public function edit(Fermeture $fermeture)
    {
        $lieux = Lieu::get()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.fermeture.form', compact('fermeture', 'lieux'));
    }

    public function update(StoreLieuFermeture $request, Fermeture $fermeture)
    {
        $fermeture->update($request->all());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Fermeture $fermeture)
    {
        $fermeture->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.lieuFermeture.index');

    }
}
