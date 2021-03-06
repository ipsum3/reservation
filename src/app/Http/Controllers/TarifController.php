<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Models\Tarif\Tarif;
use Prologue\Alerts\Facades\Alert;

class TarifController extends AdminController
{
    protected $acces = 'tarifs';

    public function edit(Saison $saison)
    {
        $tarifs = [];

        foreach($saison->tarifs as $tarif) {
            $tarifs[$tarif->modalite_paiement_id]
            [$tarif->categorie_id]
            [$tarif->duree_id] = $tarif->montant;
        }

        $durees = Duree::orderBy('min')->get();
        $categories = Categorie::orderBy('nom')->get();
        $modalites = Modalite::all();

        return view('IpsumReservation::tarif.grille', compact('saison', 'tarifs', 'durees', 'categories', 'modalites'));
    }

    public function update(Request $request, Saison $saison)
    {
        $tarifs = array();

        foreach($saison->tarifs as $tarif) {
            $tarifs[$tarif->modalite_paiement_id]
            [$tarif->categorie_id]
            [$tarif->duree_id] = $tarif;
        }

        foreach ($request->get('tarifs') as $modalite_id => $categorie_durees)
            foreach($categorie_durees as $categorie_id => $durees) {
                foreach($durees as $duree_id => $montant) {
                    if (isset($tarifs[$modalite_id][$categorie_id][$duree_id])) {
                        $tarif = $tarifs[$modalite_id][$categorie_id][$duree_id];
                    } else {
                        $tarif = new Tarif();
                        $tarif->categorie_id = $categorie_id;
                        $tarif->duree_id = $duree_id;
                        $tarif->modalite_paiement_id = $modalite_id;
                    }
                    //$montant = $montant === null ? 0 : $montant;
                    $tarif->montant = $montant;
                    $tarif->saison_id = $saison->id;
                    $tarif->save();
                }
            }
        Alert::success("La grille a bien ??t?? modifi??e")->flash();
        return back();
    }
}
