<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreDuree;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Prologue\Alerts\Facades\Alert;

class DureeController extends AdminController
{

    protected $acces = 'tarifs';

    public function index()
    {
        $durees = Duree::orderBy('min')->whereNull('type')->where('is_special', 0)->get();

        $tarifs_speciaux = Duree::orderBy('min')->whereNull('type')->where('is_special', 1)->get();

        return view('IpsumReservation::tarif.duree.index', compact('durees', 'tarifs_speciaux'));
    }

    public function create()
    {
        $duree = new Duree();
        $duree->is_special = 1;
        return view('IpsumReservation::tarif.duree.form', compact('duree'));
    }


    public function store(StoreDuree $request)
    {
        $duree = Duree::create($request->validated());

        $duree->jours()->createMany($request->jours_debut);
        $duree->jours()->createMany($request->jours_fin);

        Alert::success("L'enregistrement a bien été créé")->flash();
        return redirect()->route('admin.duree.edit', [$duree->id]);
    }

    public function edit(Duree $duree)
    {
        return view('IpsumReservation::tarif.duree.form', compact('duree'));
    }

    public function update(StoreDuree $request, Duree $duree)
    {
        $duree->fill($request->validated())->save();

        $duree->jours()->delete();
        $duree->jours()->createMany($request->jours_debut);
        $duree->jours()->createMany($request->jours_fin);

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return redirect()->route('admin.duree.edit', [$duree->id]);
    }

    public function destroy(Duree $duree)
    {
        $duree->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();
    }
    
}
