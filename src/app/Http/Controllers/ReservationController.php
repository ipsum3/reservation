<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Article\app\Models\Article;
use Ipsum\Reservation\app\Http\Requests\GetReservationVehiculeSelect;
use Ipsum\Reservation\app\Http\Requests\SendDocumentEmail;
use Ipsum\Reservation\app\Http\Requests\ShowDepartRetour;
use Ipsum\Reservation\app\Http\Requests\ShowPlanning;
use Ipsum\Reservation\app\Http\Requests\StoreAdminReservation;
use Ipsum\Reservation\app\Location\Location;
use Ipsum\Reservation\app\Location\Prestation;
use Ipsum\Reservation\app\Mail\Confirmation;
use Ipsum\Reservation\app\Mail\Devis;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Paiement;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Reservation\Type;
use Ipsum\Reservation\app\Models\Source\Source;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Options;
use OpenSpout\Writer\CSV\Writer;
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
        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->get('categorie_id'));
        }
        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->get('vehicule_id'));
        }
        if ($request->filled('condition_paiement_id')) {
            $query->where('condition_paiement_id', $request->get('condition_paiement_id'));
        }
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->get('source_id'));
        }
        if ($request->filled('date_creation')) {
            try {
                $date = explode(' - ', $request->get('date_creation'));
                $date1 = Carbon::createFromFormat('d/m/Y', $date[0])->startOfDay();
                $date2 = Carbon::createFromFormat('d/m/Y', $date[1])->endOfDay();
                $query->whereBetween('created_at', [$date1, $date2]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('date_debut')) {
            try {
                $date = explode(' - ', $request->get('date_debut'));
                $date1 = Carbon::createFromFormat('d/m/Y', $date[0])->startOfDay();
                $date2 = Carbon::createFromFormat('d/m/Y', $date[1])->endOfDay();
                $query->whereBetween('debut_at', [$date1, $date2]);
            } catch (\Exception $e) {}
        }
        if ($request->filled('date_fin')) {
            try {
                $date = explode(' - ', $request->get('date_fin'));
                $date1 = Carbon::createFromFormat('d/m/Y', $date[0])->startOfDay();
                $date2 = Carbon::createFromFormat('d/m/Y', $date[1])->endOfDay();
                $query->whereBetween('fin_at', [$date1, $date2]);
            } catch (\Exception $e) {}
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
        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        $reservationsJourQuery = Reservation::confirmed()->whereRaw("DATE(`created_at`) = CURDATE()");
        $reservationsHierQuery = Reservation::confirmed()->whereRaw("DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
        $reservationsMoisQuery = Reservation::confirmed()->whereRaw("DATE(`created_at`) BETWEEN '" . Carbon::now()->startOfMonth()->format('Y-m-d') . "' AND '" . Carbon::now()->endOfMonth()->format('Y-m-d') . "'")->get();
        $stats['hier'] = $reservationsHierQuery->count();
        $stats['jour'] = $reservationsJourQuery->count();
        $stats['mois'] = $reservationsMoisQuery->count();
        $stats['montant'] = $reservationsMoisQuery->sum('total');

        $origines = Source::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.index', compact('reservations', 'etats', 'conditions', 'categories', 'stats', 'origines'));
    }

    public function export(Request $request)
    {

        $reservations = $this->query($request)->get();

        $entete = [
            'Date',
            'Date modification',
            'Réfèrence',
            'Contrat',
            'Origine',
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

        $options = new Options();
        $options->FIELD_DELIMITER = ';';
        $options->FIELD_ENCLOSURE = '"';
        $writer = new Writer($options);
        $writer->openToBrowser($fileName);
        $row = Row::fromValues($entete);
        $writer->addRow($row);

        foreach ($reservations as $reservation) {

            $data = [
                $reservation->created_at->format('Y-m-d H:i:s'),
                $reservation->updated_at->format('Y-m-d H:i:s'),
                $reservation->reference,
                $reservation->contrat,
                $reservation->source ? $reservation->source->nom : null,
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
            $row = Row::fromValues($data);
            $writer->addRow($row);
        }
        $writer->close();

        return null;
    }

    public function create(Request $request)
    {
        $reservation = new Reservation;


        if ($request->filled('client_id')) {
            $client = Client::findOrFail($request->client_id);
            $reservation->fill($client->toArray()); // TODO pas terrible... Trouver autre chose pour peupler les données
            $reservation->client_id = $client->id;
        }

        $etats = Etat::all()->pluck('nom', 'id');
        $conditions = Condition::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');
        $prestations = Prestation::orderBy('order', 'asc')->get();
        $moyens = Moyen::all();
        $types = Type::all();

        $sources = Source::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'conditions', 'pays', 'categories', 'lieux', 'prestations', 'moyens', 'types', 'sources'));
    }

    public function store(StoreAdminReservation $request)
    {
        $reservation = new Reservation($request->validated());
        $reservation->admin_id = auth()->user()->id;
        if($request->get('create_user') == 1 && $reservation->client_id == NULL){
            $clientData = array_merge($request->validated(), ['has_login' => 0]);
            // Créer un nouveau client en base de données
            $newClient = Client::create($clientData);

            // Associer le client nouvellement créé à la réservation
            $reservation->client_id = $newClient->id;
        }
        $reservation->save();

        if ($request->validated('paiements')) {
            // Pas de mass assignment pour déclencher les événements
            $datas = [];
            foreach ($request->validated('paiements') as $paiement) {
                $datas[] = new Paiement($paiement);
            }
            $reservation->paiements()->saveMany($datas);
        }

        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.reservation.edit', $reservation);
    }

    public function edit(Reservation $reservation)
    {
        $etats = Etat::all()->pluck('nom', 'id');
        $conditions = Condition::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        if ($reservation->categorie) {
            $vehicules = $reservation->categorie->vehicules()->with('categorie')
                ->withCountReservationConfirmed($reservation->debut_at, $reservation->fin_at)
                ->withCountIntervention($reservation->debut_at, $reservation->fin_at)
                ->duParc($reservation->debut_at, $reservation->fin_at)
                ->orderBy('mise_en_circualtion_at', 'desc')
                ->get();
        } else {
            $vehicules = collect();
        }

        // Comparaison pour les conflits
        $conflicts = collect();
        if($reservation->vehicule != null){
            $conflicts = $reservation->vehicule->getConflicts($reservation);
        }

        if($reservation->client_id == null) {
            $client = Client::where('email', $reservation->email)->where('has_login', '0')->get();
            if (count($client)) {
                $reservation->client_id = $client->first()->id;
            }
        }

        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');
        $prestations = Prestation::orderBy('order', 'asc')->get();
        $moyens = Moyen::all();
        $types = Type::all();

        $sources = Source::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'conditions', 'pays', 'categories', 'lieux', 'vehicules', 'prestations', 'moyens', 'types', 'sources', 'conflicts'));
    }

    public function update(StoreAdminReservation $request, Reservation $reservation)
    {
        $data = $request->validated();
        if($request->get('create_user') == 1 && $reservation->client_id == NULL){
            $clientData = array_merge($request->validated(), ['has_login' => 0]);
            // Créer un nouveau client en base de données
            $newClient = Client::create($clientData);

            // Associer le client nouvellement créé à la réservation
            $data['client_id'] = $newClient->id;
        }

        $reservation->update($data);

        if ($request->validated('paiements')) {
            // Pas de mass assignment pour déclencher les événements
            $datas = [];
            foreach ($request->validated('paiements') as $paiement) {
                $datas[] = new Paiement($paiement);
            }
            $reservation->paiements()->saveMany($datas);
        }

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function updateTarifs(StoreAdminReservation $request, ?Reservation $reservation)
    {

        try {
            $prestations = Prestation::orderBy('order', 'asc')->get();

            if ($request->undo) {
                return response()->json([
                    'tarification' => view('IpsumReservation::reservation._tarification', compact('reservation', 'prestations'))->render(),
                ]);
            }

            $reservation->fill(collect($request->validated())->except('promotions')->all());

            config()->set('ipsum.reservation.recherche.date_format', 'Y-m-d\TH:i');
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
            $devis = $location->devis()->setRemisenAdmin($request->remise)->calculer();


            // Diff des promotions
            $promotions_diff = $reservation->promotions->keyBy('id')->diffKeys($devis->getPromotions()->keyBy('id'));

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ])->setStatusCode(422);
        }

        return response()->json([
            'tarification' => view('IpsumReservation::reservation._tarification', compact('reservation', 'devis', 'prestations', 'promotions_diff'))->render(),
        ]);
    }

    public function vehiculeSelect(GetReservationVehiculeSelect $request)
    {
        $categorie = Categorie::findOrFail($request->categorie_id);

        $debut_at = Carbon::createFromFormat('Y-m-d\TH:i', $request->debut_at);
        $fin_at = Carbon::createFromFormat('Y-m-d\TH:i', $request->fin_at);

        $vehicules = $categorie->vehicules()->with('categorie')
            ->withCountReservationConfirmed($debut_at, $fin_at)
            ->withCountIntervention($debut_at, $fin_at)
            ->duParc($debut_at, $fin_at)
            ->orderBy('mise_en_circualtion_at', 'desc')
            ->get();

        $vehicule_id = $request->vehicule_id;

        return response()->json([
            'select' => view('IpsumReservation::reservation._vehicules_select', compact('vehicules', 'vehicule_id'))->render(),
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.reservation.index');

    }

    public function devis(Reservation $reservation)
    {
        $pdf = Pdf::loadView(config('ipsum.reservation.devis.view'), compact('reservation'));
        return $pdf->stream();
    }

    public function confirmation(Reservation $reservation)
    {
        return view(config('ipsum.reservation.confirmation.view'), compact('reservation'));
    }

    public function documentSend(SendDocumentEmail $request)
    {
        try {
            $reservation = Reservation::findOrFail( $request->reservation_id );
            if( $request->document == 'confirmation' ) {
                Mail::send(new Confirmation($reservation, $request->email ));
                Alert::success("L'email de confirmation a bien été envoyé")->flash();
            } else if ( $request->document == 'devis' ) {
                Mail::send(new Devis($reservation, $request->email ));
                Alert::success("L'email de devis a bien été envoyé")->flash();
            }
            return redirect()->route('admin.reservation.edit', $reservation);
        } catch (\Exception $exception) {
            Alert::error("Impossible d'envoyer l'email")->flash();
        }

        return back();
    }

    public function reservationDocumentSend(Reservation $reservation, $document)
    {
        return view('IpsumReservation::reservation.envoi-document-informations', compact('reservation', 'document'));
    }

    public function contrat(Reservation $reservation)
    {
        $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->firstOrFail();

        $pdf = Pdf::loadView(config('ipsum.reservation.contrat.view'), compact('reservation', 'cgl'));
        return $pdf->stream();
    }

    public function contratDepart(ShowDepartRetour $request, $date = null, $lieu_id = null)
    {
        $date = $date !== null ? Carbon::createFromFormat('Y-m-d', $date) : Carbon::now();

        $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->firstOrFail();

        $query = Reservation::confirmed()
            ->where(function ($query) use ($date) {
                $query->whereRaw("DATE_FORMAT(debut_at, '%Y-%m-%d') = '".$date->format('Y-m-d')."'");
            });

        if ( $lieu_id ) {
            $query->where( 'debut_lieu_id', $lieu_id );
        }

        $reservations = $query->get();

        $html = '';
        foreach ($reservations as $reservation) {
            $html .= view(config('ipsum.reservation.contrat.view'), compact('reservation', 'cgl'))->render();
        }

        //$pdf = Pdf::loadView(config('ipsum.reservation.contrat.view'), compact('reservations', 'cgl'));
        $pdf = Pdf::loadHTML($html);
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

            $query->with(['interventions' => function ($query) use ($date_debut, $date_fin) {
                $query->with('type')->betweenDates($date_debut, $date_fin)->orderBy('debut_at');
            }])->orderBy('mise_en_circualtion_at', 'desc');
        }])->with(['reservations' => function ($query) use ($date_debut, $date_fin) {
            $query->whereNull('vehicule_id')->confirmedBetweenDates($date_debut, $date_fin)->orderBy('debut_at');
        }])->where(function ($query) {
            $query->has('vehicules')->orHas('reservations');
        })->orderBy('nom')->get();

        $categories_all = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.planning.index', compact('categories', 'date_debut', 'date_fin', 'categories_all'));
    }

    public function planningOptimiser( Categorie $categorie = null )
    {
        Artisan::call('planning:optimiser' . ($categorie ? ' --categorie=' . $categorie->id : ''));
        return back();
    }

    public function departEtRetour(ShowDepartRetour $request)
    {
        $date = $request->filled('date') ? Carbon::createFromFormat('Y-m-d', $request->date) : Carbon::now();

        $query = Reservation::confirmed()
            ->whereRaw("DATE_FORMAT(debut_at, '%Y-%m-%d') = '".$date->format('Y-m-d')."'");

        if ($request->filled('lieu_id')) {
            $query->where( 'debut_lieu_id', $request->lieu_id );
        }

        $heures_depart = $query->get()->groupBy(function (Reservation $reservation, $key) use ($date) {
            $reservation->is_debut = true;
            return  $reservation->debut_at->format('H:i');
        })->sortKeys();

        $query = Reservation::confirmed()
            ->whereRaw("DATE_FORMAT(fin_at, '%Y-%m-%d') = '".$date->format('Y-m-d')."'");

        if ($request->filled('lieu_id')) {
            $query->where( 'fin_lieu_id', $request->lieu_id );
        }

        $heures_retour = $query->get()->groupBy(function (Reservation $reservation, $key) use ($date) {
                $reservation->is_debut = false;
                return  $reservation->fin_at->format('H:i');
            })->sortKeys();

        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.depart-retour', compact('heures_depart', 'heures_retour', 'date', 'lieux'));
    }

    public function imprimerContratDepart( ShowDepartRetour $request, $date = null, $lieu_id = null )
    {
        $date = $request->filled('date') ? Carbon::createFromFormat('Y-m-d', $request->date) : Carbon::now();

        $query = Reservation::confirmed()->whereRaw("DATE_FORMAT(debut_at, '%Y-%m-%d') = '".$date->format('Y-m-d')."'");

        if( $lieu_id ) {
            $query->where( 'debut_lieu_id', $lieu_id );
        }

        $departs = $query->orderBy( 'debut_at' )->get();

        $query = Reservation::confirmed()->whereRaw("DATE_FORMAT(fin_at, '%Y-%m-%d') = '".$date->format('Y-m-d')."'");

        if( $lieu_id ) {
            $query->where( 'fin_lieu_id', $lieu_id );
        }

        $retours = $query->orderBy( 'fin_at' )->get();

        $pdf = Pdf::loadView('IpsumReservation::reservation.imprime-depart-retour', compact('departs', 'retours', 'date'));

        return $pdf->stream();
    }

    public function searchClients(Request $request) {
        $search = $request->input('client_search');
        $infos = [
            'id',
            'client_id',
            'civilite',
            'nom',
            'prenom',
            'email',
            'telephone',
            'adresse',
            'cp',
            'ville',
            'pays_id',
            'naissance_at',
            'naissance_lieu',
            'permis_numero',
            'permis_at',
            'permis_delivre'
        ];

        $clients = Client::where('email', 'like', '%' . $search . '%')
            ->where('has_login', 1)
            ->where(function($query) use ($search) {
                $query->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('prenom', 'like', '%' . $search . '%')
                ->orWhere('nom', 'like', '%' . $search . '%')
                ->orWhere('permis_numero', 'like', '%' . $search . '%');
            })
            ->limit(25)
            ->get();

        if(!$clients->count()){
            $clients = Client::where('email', 'like', '%' . $search . '%')
                ->orWhere('prenom', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
                ->orWhere('nom', 'like', '%' . $search . '%')
                ->orWhere('permis_numero', 'like', '%' . $search . '%')
                ->limit(25)
                ->get();
        }

        if(!$clients->count()){
            $clients = Reservation::whereNull('client_id')
                ->where('email', 'like', '%' . $search . '%')
                ->orWhere('client_id', 'like', '%' . $search . '%')
                ->orWhere('prenom', 'like', '%' . $search . '%')
                ->orWhere('nom', 'like', '%' . $search . '%')
                ->orWhere('permis_numero', 'like', '%' . $search . '%')
                ->limit(25)
                ->get($infos);
        }

        foreach ($clients as $client) {
            $client->text = $client->prenom . ' ' . $client->nom. ' - ' . $client->email;
            $client->is_client = get_class($client) == Client::class;
        }

        return json_encode($clients);
    }
}
