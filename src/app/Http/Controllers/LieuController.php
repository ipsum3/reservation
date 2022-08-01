<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Ipsum\Reservation\app\Classes\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreLieu;
use Ipsum\Reservation\app\Http\Requests\StoreLieuHoraire;
use Ipsum\Reservation\app\Models\Lieu\Horaire;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Lieu\Type;
use Prologue\Alerts\Facades\Alert;

class LieuController extends AdminController
{
    protected $acces = 'lieu';

    public function index(Request $request)
    {
        $query = Lieu::withCount(['fermetures' => function (Builder $query) {
            $query->where('fin_at', '>', Carbon::now())->orWhere(function (Builder $query) {
                $query->whereNull('fin_at')->where('debut_at', '<', Carbon::now());
            });
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
        $lieux = $query->orderBy('order')->paginate();

        return view('IpsumReservation::lieu.index', compact('lieux'));
    }

    public function create()
    {
        $lieu = new Lieu;
        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::lieu.form', compact('lieu', 'types'));
    }

    public function store(StoreLieu $request)
    {
        $lieu = Lieu::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.lieu.edit', [$lieu->id]);
    }

    public function edit(Lieu $lieu)
    {
        $types = Type::all()->pluck('nom', 'id');
        
        return view('IpsumReservation::lieu.form', compact('lieu', 'types'));
    }

    public function update(StoreLieu $request, Lieu $lieu)
    {
        $lieu->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Lieu $lieu)
    {
        $lieu->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.lieu.index');

    }

    public function activation(Lieu $lieu)
    {
        $lieu->is_actif = !$lieu->is_actif;
        $lieu->save();

        return redirect()->back();
    }

    public function changeOrder(Request $request)
    {
        foreach ($request->get('ids') as $key => $id) {
            $lieu = Lieu::find($id);
            if ($lieu) {
                $lieu->order = $key + 1;
                $lieu->save();
            }
        }

        return;
    }

    public function storeHoraire(StoreLieuHoraire $request, Lieu $lieu)
    {
        $horaire = $lieu->horaires()->create($request->validated());

        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return back();
    }

    public function updateHoraire(StoreLieuHoraire $request, Horaire $horaire)
    {
        $horaire->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroyHoraire(Horaire $horaire)
    {
        $horaire->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return back();
    }
}
