<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreSaison;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Prologue\Alerts\Facades\Alert;
use Symfony\Component\Console\Output\BufferedOutput;

class SaisonController extends AdminController
{
    protected $acces = 'tarifs';

    public function index(Request $request)
    {
        $query = Saison::query();

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
        $saisons = $query->orderBy('debut_at', 'desc')->paginate();

        try {
            $output = new BufferedOutput();
            Artisan::call('reservation:check --type=saison', [], $output);
            $result = $output->fetch();
            $lines = explode("\n", trim($result));

            foreach ($lines as $line) {
                if (!empty(trim($line))) { // Éviter les alertes vides
                    Alert::warning($line);
                }
            }
        } catch (\Exception $e) { }

        return view('IpsumReservation::tarif.saison.index', compact('saisons'));
    }

    public function create()
    {
        $saison = new Saison;

        return view('IpsumReservation::tarif.saison.form', compact('saison'));
    }

    public function store(StoreSaison $request)
    {
        $saison = Saison::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.saison.edit', [$saison->id]);
    }

    public function edit(Saison $saison)
    {
        $tarifs = [];

        foreach($saison->tarifs as $tarif) {
            $tarifs[$tarif->condition_paiement_id]
            [$tarif->categorie_id]
            [$tarif->duree_id] = $tarif->montant;
        }

        $durees = Duree::orderBy('min')->get();
        $categories = Categorie::orderBy('nom')->get();
        $conditions = config('ipsum.reservation.tarif.has_multiple_grille_by_condition') ? Condition::where('site_actif', 1)->get() : null;

        return view('IpsumReservation::tarif.saison.form', compact('saison', 'tarifs', 'durees', 'categories', 'conditions'));
    }

    public function update(StoreSaison $request, Saison $saison)
    {
        $saison->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Saison $saison)
    {
        $saison->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.saison.index');

    }

    public function cloner(Saison $saison)
    {
        $saison_clone = $saison->replicateWithTarifs();

        return redirect()->route('admin.saison.edit', [$saison_clone->id]);
    }
}
