<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreVehicule;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Ipsum\Reservation\app\Models\Dommage\Dommage;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Prologue\Alerts\Facades\Alert;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;

class VehiculeController extends AdminController
{
    protected $acces = 'vehicule';

    public function index(Request $request)
    {
        $query = Vehicule::with(['categorie'])->withCount(['reservations' => function (Builder $query) {
            $query->confirmed()->where('fin_at', '>', Carbon::now());
        }]);


        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->get('categorie_id'));
        }

        if ($request->filled('etat')) {

            if ($request->etat === 'hors_parc') {
                $query->horsParc(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());
            }

            if ($request->etat === 'parc') {
                $query->duParc(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());
            }

        }

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['id', 'marque_modele', 'immatriculation'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }
        $vehicules = $query->orderBy('immatriculation')->paginate();

        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.vehicule.index', compact('vehicules', 'categories'));
    }

    public function create()
    {
        $vehicule = new Vehicule;

        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.vehicule.form', compact('vehicule', 'categories'));
    }

    public function store(StoreVehicule $request)
    {
        $vehicule = Vehicule::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.vehicule.edit', [$vehicule->id]);
    }

    public function edit(Vehicule $vehicule)
    {
        $vehicule->load(['reservations' => function ($query) {
            $query->confirmed()->where('fin_at', '>', Carbon::now())->orderBy('debut_at', 'asc')->limit('20');
        },
        'interventions' => function ($query) {
            $query->where('fin_at', '>=', Carbon::now())->orderBy('debut_at', 'asc')->limit('20');
        }]);

        $types = Type::get()->pluck('nom', 'id');
        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        //Taux de rotation annuel
        $reservations = Reservation::where('vehicule_id', $vehicule->id)->where('debut_at', '<', Carbon::now())->confirmed()->get();
        $nbJourLocation = 0;
        foreach ( $reservations as $reservation ) {
            $fin = $reservation->fin_at > Carbon::now() ? Carbon::now() : $reservation->fin_at;
            $nbJourLocation += $fin->diffInHours($reservation->debut_at) / 24;
        }
        $date = Carbon::parse($vehicule->entree_at);
        $now = Carbon::now();
        $nbJourVehicule = $date->diffInHours($now) / 24;
        $stats['tauxRotation'] = round( ($nbJourLocation * 100) / $nbJourVehicule, 2);
        $reservations = Reservation::where('vehicule_id', $vehicule->id)->where('fin_at', '<', Carbon::now())->confirmed();
        $stats['reservation'] = $reservations->count();
        $stats['montants'] = $reservations->sum('total');

        $conflicts = $vehicule->getConflicts();

        return view('IpsumReservation::categorie.vehicule.form', compact('vehicule', 'types', 'categories', 'stats', 'conflicts'));
    }

    public function update(StoreVehicule $request, Vehicule $vehicule)
    {
        $vehicule->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Vehicule $vehicule)
    {
        $vehicule->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.vehicule.index');

    }

    public function dommage_destroy(Vehicule $vehicule, Dommage $dommage)
    {
        $dommage->delete();

        Alert::warning("Le dommage a bien été supprimé")->flash();
        return back();

    }

    public function export(Request $request)
    {
        $vehicules = Vehicule::with(['categorie'])
            ->withCount(['reservations' => function (Builder $query) {
                $query->confirmed()->where('fin_at', '>', Carbon::now());
            }]);

        if ($request->filled('categorie_id')) {
            $vehicules->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('etat')) {

            if ($request->etat === 'hors_parc') {
                $vehicules->horsParc(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());
            }

            if ($request->etat === 'parc') {
                $vehicules->duParc(Carbon::now()->startOfDay(), Carbon::now()->endOfDay());
            }
        }

        if ($request->filled('search')) {
            $vehicules->where(function ($query) use ($request) {
                foreach (['id', 'marque_modele', 'immatriculation'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%' . $request->search . '%');
                }
            });
        }

        if ($request->filled('tri')) {
            $vehicules->orderBy($request->tri, $request->order);
        } else {
            $vehicules->orderBy('immatriculation');
        }

        $vehicules = $vehicules->get();

        $entete = [
            '#',
            'Immatriculation',
            'Marque / Modèle',
            'Catégorie',
            'Réservations futures',
            'État',
            'Date de mise en circulation*',
            'Date d\'entrée',
            'Date sortie',
            'Kilométrage',
        ];


        $fileName = "export-vehicules-" . date('d-m-Y_H-i-s') . ".csv";

        $options = new Options();
        $options->FIELD_DELIMITER = ';';
        $options->FIELD_ENCLOSURE = '"';

        $writer = new Writer($options);
        $writer->openToBrowser($fileName);

        $writer->addRow(Row::fromValues($entete));

        foreach ($vehicules as $vehicule) {

            $data = [
                $vehicule->id,
                $vehicule->immatriculation,
                $vehicule->marque_modele,
                optional($vehicule->categorie)->nom,
                $vehicule->reservations_count,
                $vehicule->is_hors_parc ? 'Hors parc' : 'En parc',
                $vehicule->mise_en_circualtion_at ? Carbon::parse($vehicule->mise_en_circualtion_at)->format('d/m/Y') : null,
                $vehicule->entree_at ? Carbon::parse($vehicule->entree_at)->format('d/m/Y') : null,
                $vehicule->sortie_at ? Carbon::parse($vehicule->sortie_at)->format('d/m/Y') : null,
                config('ipsum.reservation.etat_des_lieux.enable') && $vehicule->last_inspection ? $vehicule->last_inspection->kilometrage ?? '' : '',
            ];

            $writer->addRow(Row::fromValues($data));
        }

        $writer->close();

        return null;
    }
}
