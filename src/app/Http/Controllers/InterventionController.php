<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreIntervention;
use Ipsum\Reservation\app\Models\Categorie\Intervention;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;
use Prologue\Alerts\Facades\Alert;

class InterventionController extends AdminController
{
    protected $acces = 'vehicule';

    protected function query(Request $request)
    {
        $query = Intervention::with(['vehicule', 'type']);

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->get('vehicule_id'));
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->get('type_id'));
        }
        if ($request->filled('immatriculation')) {
            $query->whereHas('vehicule', function ($query) use ($request) {
                $query->where('immatriculation', $request->get('immatriculation'));
            });
        }
        if ($request->filled('dates')) {
            try {
                $date = explode(' - ', $request->get('dates'));
                $date1 = Carbon::createFromFormat('d/m/Y', $date[0])->startOfDay();
                $date2 = Carbon::createFromFormat('d/m/Y', $date[1])->endOfDay();
                $query->betweenDates($date1, $date2);
            } catch (\Exception $e) {}
        }

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['intervenant', 'information'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function index(Request $request)
    {

        $interventions = $this->query($request)->paginate();

        $types = InterventionType::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::categorie.intervention.index', compact('interventions', 'types'));
    }

    public function export(Request $request)
    {

        $interventions = $this->query($request)->get();

        $entete = [
            '#',
            'Véhicule',
            'Type',
            'Début',
            'Fin',
            'Intervenant',
            'Information',
            'Kilométrage',
        ];

        if (config('ipsum.reservation.client.custom_fields')) {
            foreach (config('ipsum.reservation.client.custom_fields') as $field) {
                $entete[] = $field['label'];
            }
        }

        $fileName = "export-intervention-" . date('d-m-Y_H-i-s') . ".csv";

        $options = new Options();
        $options->FIELD_DELIMITER = ';';
        $options->FIELD_ENCLOSURE = '"';
        $writer = new Writer($options);
        $writer->openToBrowser($fileName);
        $row = Row::fromValues($entete);
        $writer->addRow($row);

        foreach ($interventions as $intervention) {

            $data = [
                $intervention->id,
                $intervention->vehicule?->immatriculation,
                $intervention->type?->nom,
                $intervention->debut_at->format('Y-m-d H:i'),
                $intervention->fin_at->format('Y-m-d H:i'),
                $intervention->intervenant,
                $intervention->information,
                $intervention->km,

            ];
            if (config('ipsum.reservation.client.custom_fields')) {
                foreach (config('ipsum.reservation.client.custom_fields') as $field) {
                    $data[] = $intervention->custom_fields->{$field['name']};
                }
            }

            $row = Row::fromValues($data);
            $writer->addRow($row);
        }
        $writer->close();

        return null;
    }

    public function create()
    {
        $intervention = new Intervention;

        $types = InterventionType::orderBy('order')->get()->pluck('nom', 'id');
        $vehicules = Vehicule::orderBy('immatriculation')->get()->mapWithKeys(function ($vehicule) {
            return [$vehicule->id => $vehicule->categorie ? $vehicule->categorie->nom.' : '.$vehicule->immatriculation.' '.$vehicule->marque_modele : $vehicule->immatriculation.' '.$vehicule->marque_modele ];
        });

        return view('IpsumReservation::categorie.intervention.form', compact('intervention', 'types', 'vehicules'));
    }

    public function store(StoreIntervention $request)
    {
        $intervention = Intervention::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.intervention.edit', [$intervention->id]);
    }

    public function edit(Intervention $intervention)
    {
        $types = InterventionType::orderBy('order')->get()->pluck('nom', 'id');
        $vehicules = Vehicule::orderBy('immatriculation')->get()->mapWithKeys(function ($vehicule) {
            return [$vehicule->id => $vehicule->categorie ? $vehicule->categorie->nom.' : '.$vehicule->immatriculation.' '.$vehicule->marque_modele : $vehicule->immatriculation.' '.$vehicule->marque_modele ];
        });

        return view('IpsumReservation::categorie.intervention.form', compact('intervention', 'types', 'vehicules'));
    }

    public function update(StoreIntervention $request, Intervention $intervention)
    {
        $intervention->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Intervention $intervention)
    {
        $intervention->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.intervention.index');

    }
}
