<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreCarrosserie;
use Ipsum\Reservation\app\Models\Categorie\Carrosserie;
use Prologue\Alerts\Facades\Alert;

class CarrosserieController extends AdminController
{

    protected $acces = 'categorie';

    public function index(Request $request)
    {
        $query = Carrosserie::query();

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['nom'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        $carrosseries = $query->orderBy('order')->paginate();

        return view('IpsumReservation::categorie.carrosserie.index', compact('carrosseries'));
    }

    public function create()
    {
        $carrosserie = new Carrosserie;

        return view('IpsumReservation::categorie.carrosserie.form', compact('carrosserie'));
    }

    public function store(StoreCarrosserie $request)
    {
        $carrosserie = Carrosserie::create($request->validated());

        Alert::success("L'enregistrement a bien été créé")->flash();
        return redirect()->route('admin.carrosserie.index');
    }

    public function edit(Carrosserie $carrosserie)
    {
        return view('IpsumReservation::categorie.carrosserie.form', compact('carrosserie'));
    }

    public function update(StoreCarrosserie $request, Carrosserie $carrosserie)
    {
        $carrosserie->fill($request->validated())->save();

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return redirect()->route("admin.carrosserie.index");
    }

    public function destroy(Carrosserie $carrosserie)
    {
        $carrosserie->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();
    }

    public function changeOrder(Request $request)
    {
        foreach ($request->get('ids') as $key => $id) {
            $carrosserie = Carrosserie::find($id);
            if ($carrosserie) {
                $carrosserie->order = $key + 1;
                $carrosserie->save();
            }
        }

        return;
    }
    
}
