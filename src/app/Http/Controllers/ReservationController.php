<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Article\app\Models\Article;
use Ipsum\Reservation\app\Http\Requests\ShowPlanning;
use Ipsum\Reservation\app\Http\Requests\StoreAdminReservation;
use Ipsum\Reservation\app\Location\Location;
use Ipsum\Reservation\app\Location\Prestation;
use Ipsum\Reservation\app\Mail\Confirmation;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Prologue\Alerts\Facades\Alert;

class ReservationController extends AdminController
{
    protected $acces = 'reservation';

    protected function query(Request $request)
    {
        $query = Reservation::with('etat', 'condition', 'client');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }
        if ($request->filled('etat_id')) {
            $query->where('etat_id', $request->get('etat_id'));
        }
        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->get('vehicule_id'));
        }
        if ($request->filled('condition_paiement_id')) {
            $query->where('condition_paiement_id', $request->get('condition_paiement_id'));
        }
        if ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->get('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->get('date_fin'));
        }
        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['reference', 'contrat', 'nom', 'prenom', 'email', 'telephone'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }
        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }

        return $query->latest();
    }

    public function index(Request $request)
    {

        $reservations = $this->query($request)->paginate();

        $etats = Etat::all()->pluck('nom', 'id');
        $conditions = Condition::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.index', compact('reservations', 'etats', 'conditions'));
    }

    public function export(Request $request)
    {

        $reservations = $this->query($request)->get();

        $entete = [
            'Date',
            'Date modification',
            'Réfèrence',
            'Contrat',
            'Email',
            'Nom',
            'Prénom',
            'Téléphone',
            'Adresse',
            'Cp',
            'Ville',
            'Pays',
            'Date de naissance',
            'Numéro de permis',
            'Date permis',
            'Permis délivré par',
            'Observation',
            'Catégorie',
            'Franchise',
            'Date début',
            'Date fin',
            'Début',
            'Fin',
            'Montant de base',
            'Total',
            'Montant payé',
            'Note',
            'Etat',
            'Condition',
        ];


        $fileName = "export-reservation-" . date('d-m-Y_H-i-s') . ".csv";

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->setFieldDelimiter(';');
        $writer->setFieldEnclosure('"');
        $writer->openToBrowser($fileName);
        $row = WriterEntityFactory::createRowFromArray($entete);
        $writer->addRow($row);

        foreach ($reservations as $reservation) {

            $data = [
                $reservation->created_at,
                $reservation->updated_at,
                $reservation->reference,
                $reservation->contrat,
                $reservation->email,
                $reservation->nom,
                $reservation->prenom,
                $reservation->telephone,
                $reservation->adresse,
                $reservation->cp,
                $reservation->ville,
                $reservation->pays_nom,
                $reservation->naissance_at ? $reservation->naissance_at->format('d/m/Y') : null,
                $reservation->permis_numero,
                $reservation->permis_at ? $reservation->permis_at->format('d/m/Y') : null,
                $reservation->permis_delivre,
                $reservation->observation,
                $reservation->categorie_nom,
                $reservation->franchise,
                $reservation->debut_at ? $reservation->debut_at->format('d/m/Y') : null,
                $reservation->fin_at ? $reservation->fin_at->format('d/m/Y') : null,
                $reservation->debut_lieu_nom,
                $reservation->fin_lieu_nom,
                $reservation->montant_base,
                $reservation->total,
                $reservation->montant_paye,
                $reservation->note,
                $reservation->etat ? $reservation->etat->nom : null,
                $reservation->condition ? $reservation->condition->nom : null,
            ];
            $row = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($row);
        }
        $writer->close();

        return null;
    }

    public function create(Request $request)
    {
        $reservation = new Reservation;


        if ($request->filled('client_id')) {
            $client = config('ipsum.reservation.client.model')::findOrFail($request->client_id);
            $reservation->fill($client->toArray()); // TODO pas terrible... Trouver autre chose pour peupler les données
        }

        $etats = Etat::all()->pluck('nom', 'id');
        $conditions = Condition::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::all()->pluck('nom', 'id');
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');
        $prestations = Prestation::orderBy('order', 'asc')->get();

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'conditions', 'pays', 'categories', 'lieux', 'prestations'));
    }

    public function store(StoreAdminReservation $request)
    {
        $reservation = new Reservation($request->validated());
        $reservation->admin_id = auth()->user()->id;
        $reservation->save();

        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.reservation.edit', $reservation);
    }

    public function edit(Reservation $reservation)
    {
        $etats = Etat::all()->pluck('nom', 'id');
        $conditions = Condition::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::all()->pluck('nom', 'id');
        $vehicules = Vehicule::with('categorie')
            ->where(function ($query) use ($reservation) {
                $query->whereDoesntHaveReservationConfirmed($reservation->debut_at, $reservation->fin_at)->orWhere('id', $reservation->vehicule_id);
            })
            ->orderBy('categorie_id')->orderBy('immatriculation')
            ->get()
            ->mapWithKeys(function ($vehicule) {
                return [$vehicule->id => $vehicule->categorie->nom.' : '.$vehicule->immatriculation.' '.$vehicule->marque_modele];
            });
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');
        $prestations = Prestation::orderBy('order', 'asc')->get();

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'conditions', 'pays', 'categories', 'lieux', 'vehicules', 'prestations'));
    }

    public function update(StoreAdminReservation $request, Reservation $reservation)
    {
        $reservation->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function updateTarifs(StoreAdminReservation $request, ?Reservation $reservation)
    {

        try {

            $reservation->fill(collect($request->validated())->except('promotions')->all());

            config()->set('ipsum.reservation.recherche.date_format', 'Y-m-d H:i:s');
            config()->set('ipsum.reservation.recherche.jour_format', 'Y-m-d');
            $location = new Location;
            $location->setRecherche($request->all());
            $location->setCondition($reservation->condition);
            $location->setCategorie(\Ipsum\Reservation\app\Location\Categorie::findOrFail($reservation->categorie_id));
            $location->setPrestations(collect($request->prestations)->map(function ($prestation) {
                if (!empty($prestation['quantite'])) {
                    $prestation['has'] = $prestation['id'];
                }
                return $prestation;
            })->all());
            $devis = $location->devis()->calculer();

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ])->setStatusCode(422);
        }

        $prestations = Prestation::orderBy('order', 'asc')->get();

        return response()->json([
            'tarification' => view('IpsumReservation::reservation._tarification', compact('reservation', 'devis', 'prestations'))->render(),
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.reservation.index');

    }

    public function confirmation(Reservation $reservation)
    {
        return view(config('ipsum.reservation.confirmation.view'), compact('reservation'));
    }

    public function confirmationSend(Reservation $reservation)
    {
        try {

            Mail::send(new Confirmation($reservation));
            Alert::success("L'email de confirmation a bien été envoyé")->flash();

        } catch (\Exception $exception) {
            Alert::error("Impossible d'envoyer l'email")->flash();
        }

        return back();
    }

    public function contrat(Reservation $reservation)
    {
        $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->firstOrFail();

        $pdf = Pdf::loadView(config('ipsum.reservation.contrat.view'), compact('reservation', 'cgl'));
        return $pdf->stream();
    }

    public function planning(ShowPlanning $request)
    {
        $date_debut = $request->filled('date_debut') ? Carbon::createFromFormat('Y-m-d', $request->date_debut) : Carbon::now()->subDays(4)->startOfDay();
        $date_fin = $request->filled('date_fin') ? Carbon::createFromFormat('Y-m-d', $request->date_fin) : $date_debut->copy()->addMonths(3);

        $categories = Categorie::when($request->categorie_id, function ($query, $categorie_id) {
            $query->where('id', $categorie_id);
        })->with(['vehicules' => function ($query) use ($date_debut, $date_fin) {
            $query->with(['reservations' => function ($query) use ($date_debut, $date_fin) {
                $query->confirmedBetweenDates($date_debut, $date_fin)->orderBy('debut_at');
            }])->orderBy('mise_en_circualtion_at', 'desc');
        }])->with(['reservations' => function ($query) use ($date_debut, $date_fin) {
            $query->whereNull('vehicule_id')->confirmedBetweenDates($date_debut, $date_fin)->orderBy('debut_at');
        }])->where(function ($query) {
            $query->has('vehicules')->orHas('reservations');
        })->orderBy('nom')->get();

        $categories_all = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.planning.index', compact('categories', 'date_debut', 'date_fin', 'categories_all'));
    }

    public function planningOptimiser()
    {
        Artisan::call('planning:optimiser');
        return back();
    }
}
