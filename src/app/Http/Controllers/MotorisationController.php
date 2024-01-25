<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreMotorisation;
use Ipsum\Reservation\app\Models\Categorie\Motorisation;
use Alert;

class MotorisationController extends AdminController
{


    public function index(Request $request)
    {
        $query = Motorisation::query();

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['nom'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        $motorisations = $query->paginate();

        return view('IpsumReservation::motorisation.index', compact('motorisations'));
    }

    public function create()
    {
        $motorisation = new Motorisation;

        return view('IpsumReservation::motorisation.form', compact('motorisation'));
    }

    public function store(StoreMotorisation $request)
    {
        $motorisation = Motorisation::create($request->validated());

        Alert::success("L'enregistrement a bien été créé")->flash();
        return redirect()->route('admin.carrosserie.index');
    }

    public function edit(Motorisation $motorisation)
    {
        return view('IpsumReservation::motorisation.form', compact('motorisation'));
    }

    public function update(StoreMotorisation $request, Motorisation $motorisation)
    {
        $motorisation->fill($request->validated())->save();

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return redirect()->route("admin.motorisation.index");
    }

    public function destroy(Motorisation $motorisation)
    {
        $motorisation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();
    }
}
