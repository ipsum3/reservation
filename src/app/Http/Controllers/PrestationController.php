<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StorePrestation;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Type;
use Prologue\Alerts\Facades\Alert;

class PrestationController extends AdminController
{
    protected $acces = 'tarif';

    public function index(Request $request)
    {
        $query = Prestation::with(['type'])->withCount(['blocages' => function (Builder $query) {
            $query->where('fin_at', '>', Carbon::now());
        }]);

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
        $prestations = $query->orderBy('order')->paginate();

        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.index', compact('prestations', 'types'));
    }

    public function create()
    {
        $prestation = new Prestation;
        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::prestation.form', compact('prestation', 'types'));
    }

    public function store(StorePrestation $request)
    {
        $prestation = Prestation::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.prestation.edit', [$prestation->id]);
    }

    public function edit(Prestation $prestation)
    {
        $types = Type::all()->pluck('nom', 'id');
        
        return view('IpsumReservation::prestation.form', compact('prestation', 'types'));
    }

    public function update(StorePrestation $request, Prestation $prestation)
    {
        $prestation->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Prestation $prestation)
    {
        $prestation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.prestation.index');

    }

    public function changeOrder(Request $request)
    {
        foreach ($request->get('ids') as $key => $id) {
            $prestation = Prestation::find($id);
            if ($prestation) {
                $prestation->order = $key + 1;
                $prestation->save();
            }
        }

        return;
    }
}
