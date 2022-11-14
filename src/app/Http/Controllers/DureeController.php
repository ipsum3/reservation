<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreDuree;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Prologue\Alerts\Facades\Alert;

class DureeController extends AdminController
{

    protected $acces = 'tarif';

    public function index()
    {
        $durees = Duree::orderBy('min')->whereNull('type')->where('is_special', 0)->get();

        $tarifs_speciaux = Duree::orderBy('min')->whereNull('type')->where('is_special', 1)->get();

        return view('IpsumReservation::tarif.duree.index', compact('durees', 'tarifs_speciaux'));
    }


    public function store(StoreDuree $request)
    {
        Duree::create($request->validated());

        Alert::success("L'enregistrement a bien été créé")->flash();
        return back();
    }

    public function edit(Duree $duree)
    {
        return view('IpsumReservation::tarif.duree.form', compact('duree'));
    }

    public function update(StoreDuree $request, Duree $duree)
    {
        $duree->fill($request->validated())->save();

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return redirect()->route("admin.duree.index");
    }

    public function destroy(Duree $duree)
    {
        $duree->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();
    }
    
}
