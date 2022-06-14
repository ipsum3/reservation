<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreLieuFerie;
use Ipsum\Reservation\app\Models\Lieu\Ferie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Prologue\Alerts\Facades\Alert;

class LieuFerieController extends AdminController
{
    protected $acces = 'lieu';

    public function index(Request $request)
    {
        $query = Ferie::with('lieu');

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
        $feries = $query->orderBy('jour_at', 'desc')->paginate();

        $lieux = Lieu::get()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.ferie.index', compact('feries', 'lieux'));
    }

    public function create()
    {
        $ferie = new Ferie;

        $lieux = Lieu::get()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.ferie.form', compact('ferie', 'lieux'));
    }

    public function store(StoreLieuFerie $request)
    {
        $ferie = Ferie::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.lieuFerie.edit', [$ferie->id]);
    }

    public function edit(Ferie $ferie)
    {
        $lieux = Lieu::get()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.ferie.form', compact('ferie', 'lieux'));
    }

    public function update(StoreLieuFerie $request, Ferie $ferie)
    {
        $ferie->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Ferie $ferie)
    {
        $ferie->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.lieuFerie.index');

    }
}
