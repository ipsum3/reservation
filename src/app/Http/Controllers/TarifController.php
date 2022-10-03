<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Reservation\Condition;
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
            $tarifs[$tarif->condition_paiement_id]
            [$tarif->categorie_id]
            [$tarif->duree_id] = $tarif->montant;
        }

        $durees = Duree::orderBy('min')->get();
        $categories = Categorie::orderBy('nom')->get();
        $conditions = config('ipsum.reservation.tarif.has_multiple_grille_by_condition') ? Condition::all() : null;

        return view('IpsumReservation::tarif.grille', compact('saison', 'tarifs', 'durees', 'categories', 'conditions'));
    }

    public function update(Request $request, Saison $saison)
    {
        $tarifs = array();

        foreach($saison->tarifs as $tarif) {
            $tarifs[$tarif->condition_paiement_id ?? 'x']
            [$tarif->categorie_id]
            [$tarif->duree_id] = $tarif;
        }

        foreach ($request->get('tarifs') as $condition_id => $categorie_durees)
            foreach($categorie_durees as $categorie_id => $durees) {
                foreach($durees as $duree_id => $montant) {
                    if (isset($tarifs[$condition_id][$categorie_id][$duree_id])) {
                        $tarif = $tarifs[$condition_id][$categorie_id][$duree_id];
                    } else {
                        $tarif = new Tarif();
                        $tarif->categorie_id = $categorie_id;
                        $tarif->duree_id = $duree_id;
                        $tarif->condition_paiement_id = $condition_id == 'x' ? null : $condition_id;
                    }
                    //$montant = $montant === null ? 0 : $montant;
                    $tarif->montant = $montant;
                    $tarif->saison_id = $saison->id;
                    $tarif->save();
                }
            }
        Alert::success("La grille a bien été modifiée")->flash();
        return back();
    }
}
