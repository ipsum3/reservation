<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreSource;
use Ipsum\Reservation\app\Models\Source\Source;
use Ipsum\Reservation\app\Models\Source\Type;
use Prologue\Alerts\Facades\Alert;
use Str;

class SourceController extends AdminController
{
    protected $acces = 'source';

    public function index(Request $request)
    {
        $query = Source::query();

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['nom'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->get('type_id'));
        }
        $sources = $query->paginate();

        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::source.index', compact('sources', 'types'));
    }

    public function create()
    {
        $source = new Source;
        $types = Type::all()->pluck('nom', 'id');
        return view('IpsumReservation::source.form', compact('source', 'types'));
    }

    public function store(StoreSource $request)
    {
        $datas = $request->validated();
        $datas['ref_tracking'] = Str::random(30);
        $source = Source::create( $datas );
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.source.edit', [$source->id]);
    }

    public function edit(Source $source)
    {
        $types = Type::all()->pluck('nom', 'id');
        return view('IpsumReservation::source.form', compact('source', 'types'));
    }

    public function update(StoreSource $request, Source $source)
    {
        $source->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Source $source)
    {
        $source->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.source.index');

    }

}
