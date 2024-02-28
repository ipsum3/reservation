<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Source\Source;

class StatistiqueController extends ReservationController
{
    protected $acces = 'statistique';

    public function index(Request $request){
        $reservations = $this->query($request)->get();

        $etats = Etat::all()->pluck('nom', 'id');
        $conditions = Condition::all()->pluck('nom', 'id');
        $categories = Categorie::orderBy('nom')->get()->pluck('nom', 'id');

        $type_date = "created_at";
        if($request->filled('type_date')){
            $type_date = $request->get('type_date');
        }

        if ($request->filled('periode')) {
            $dates = (explode(" - ",$request->get('periode')));
            $dateDebut = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $dateFin = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $dateFin = Carbon::now()->endOfMonth();
            $dateDebut = $dateFin->copy()->subMonths(11)->startOfMonth();
            $request->merge(['periode' => $dateDebut->format('d/m/Y').' - '. $dateFin->format('d/m/Y') ]);
        }

        $reservationsTransactionQuery = Reservation::confirmed()->whereBetween($type_date, [$dateDebut, $dateFin])->get();

        $reservationsJourQuery = Reservation::confirmed()->whereRaw("DATE(`debut_at`) = CURDATE()");
        $reservationsHierQuery = Reservation::confirmed()->whereRaw("DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
        $reservationsEnCoursQuery = Reservation::confirmed()->whereDate('debut_at', '<=', Carbon::now())->whereDate('fin_at', '>=', Carbon::now())->get();
        $reservationsIncomingQuery = Reservation::confirmed()->whereDate('debut_at', '>=', Carbon::now())->get();
        $stats['hier'] = $reservationsHierQuery->count();
        $stats['jour'] = $reservationsJourQuery->count();
        $stats['montant'] = $reservationsTransactionQuery->sum('total');

        $stats['en_cours'] = $reservationsEnCoursQuery->count();
        $stats['a_venir'] = $reservationsIncomingQuery->count();

        //volume par transaction


        $stats = array_merge($stats, $this->getStats($reservationsTransactionQuery, $type_date, $dateDebut, $dateFin));

        if ($request->filled('prev_periode')) {
            $dateDebutPrev = $dateDebut->copy()->subYear();
            $dateFinPrev = $dateFin->copy()->subYear();
            $reservationsTransactionQueryPrev = Reservation::confirmed()->whereBetween($type_date, [$dateDebutPrev, $dateFinPrev])->get();
            $stats['previous'] = $this->getStats($reservationsTransactionQueryPrev, $type_date, $dateDebutPrev, $dateFinPrev);
        }

        $stats['maxReservationCount'] = max($stats['reservationCountData']);
        $stats['maxMontantTotal'] = max($stats['montantTotalData']);

        //taux de rotation
        $totalJoursLocation = 0;
        $nombreJoursDuree = $dateDebut->diffInDays($dateFin) + 1;

        $nombreVoituresActivees =  Vehicule::EnService($dateDebut, $dateFin)->count();

        foreach ($reservationsTransactionQuery as $reservation) {
            // Ajouter le nombre de jours de location au total
            $totalJoursLocation += $reservation->nb_jours;
        }

        // Calcul du taux de rotation mensuel
        if(($nombreVoituresActivees * $nombreJoursDuree) <=0){
            $stats['taux_rotation'] = 0;
        }else{
            $stats['taux_rotation'] = ($totalJoursLocation / ($nombreVoituresActivees * $nombreJoursDuree)) * 100;
        }

        //réservation par source
        $sources = Source::orderBy('nom')->get();
        $stats['reservationsParSource'] = $reservationsTransactionQuery->groupBy('source')
            ->map(function ($reservations, $source) {
                if($reservations->first()->source){
                    return ['label' => $reservations->first()->source->nom, 'count' => count($reservations)];
                }else{
                    return ['label' => 'Non renseigné', 'count' => count($reservations)];
                }
            })->values()->toArray();
        foreach ($sources as $source ){
            $found = 0;
            foreach ($stats['reservationsParSource'] as $reservation){
                if($reservation['label'] == $source['nom']){
                    $found=1;
                }
            }
            if($found == 0){
                $stats['reservationsParSource'][] = [
                    "label" => $source['nom'],
                    "count" => 0,
                ];
            }
        }

        // cout moyen d'une par jour par catégorie
        $stats['reservationsParCategorie'] = $reservationsTransactionQuery->groupBy('categorie_nom')
            ->map(function (Collection $reservations, $categorie) {
                $count = count($reservations);

                // On prend le montant de base pour le calcul pour ne pas prendre en compte les prestations
                // Par contre s'il y a une promotion sur le montant de base, cela ne sera pas pris en compte
                // TODO déduire le montant total des prestations à total ?
                $totalCost = $reservations->sum('montant_base');

                // Calculer le nombre total de jours pour toutes les réservations de cette catégorie
                $totalDays = $reservations->reduce(function ($totalDays, $reservation) {
                    return $totalDays + $reservation->nb_jours;
                }, 0);

                $averageCost = $count > 0 ? $totalCost / $count : 0;
                $averageCostByDay = $totalDays > 0 ? $totalCost / $totalDays : 0;

                return [
                    'label' => $categorie,
                    'count' => $count,
                    'average_cost' => $averageCost,
                    'average_costs_by_day' => $averageCostByDay,
                ];
            })->values()->toArray();

        usort($stats['reservationsParCategorie'], function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        // taux d'annulation
        $totalConfirmedReservations = $reservationsTransactionQuery->count();
        $totalCancelledReservations = Reservation::where('etat_id', '5')->whereBetween($type_date, [$dateDebut, $dateFin])->count();
        // Calculer le taux d'annulation de réservation
        //dd($totalCancelledReservations,$totalConfirmedReservations);
        if($totalConfirmedReservations <=0){
            $annulationRate = 0;
        }else{
            $annulationRate = ($totalCancelledReservations / $totalConfirmedReservations) * 100;
        }
        // Arrondir le taux d'annulation à deux décimales
        $stats['annulationRate'] = round($annulationRate, 2);

        // Compter le nombre de réservations par état
        $stats['reservationsParEtat'] = Reservation::whereBetween($type_date, [$dateDebut, $dateFin])->get()->groupBy('etat_id')
            ->map(function ($reservations, $etat) {
                return ['label' => $reservations->first()->etat->nom, 'count' => count($reservations)];
            })->values()->toArray();

        // top 5 lieu de réservation
        $reservationsParLieu = $reservationsTransactionQuery->groupBy('debut_lieu_nom')
            ->map(function ($reservations, $lieu) {
                return ['label' => $lieu, 'count' => count($reservations)];
            });

        // Trier les lieux par ordre décroissant du nombre de réservations
        $reservationsParLieu = $reservationsParLieu->sortByDesc('count');

        // Séparer les 5 premiers lieux du reste
        $topLieux = $reservationsParLieu->take(5);
        $otherLieux = $reservationsParLieu->slice(5);

        // Calculer le nombre total de réservations pour les autres lieux
        $others = $otherLieux->sum('count');

        // Ajouter "Autres" avec le nombre total de réservations restantes
        if($others){
            $topLieux->push(['label' => 'Autres', 'count' => $others]);
        }

        // Affecter le nouveau tableau à $stats['reservationsParLieu']
        $stats['reservationsParLieu'] = $topLieux->values()->toArray();

        //affihcer la liste des véhicules
        $all_vehicules = Vehicule::with(['categorie'])->withCount(['reservations' => function (Builder $query) {
            $query->confirmed()->where('fin_at', '>', Carbon::now());
        }]);

        $origines = Source::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.statistiques', compact('reservations', 'etats', 'conditions', 'categories', 'stats', 'origines'));
    }

    public function getListeMois($dateDebut, $dateFin)
    {
        $listeMois = [];

        $dateCourante = Carbon::parse($dateDebut);
        $dateFin = Carbon::parse($dateFin);

        while ($dateCourante->lte($dateFin)) {
            $listeMois[] = $dateCourante->format('F Y');
            $dateCourante->firstOfMonth()->addMonth();
        }

        return $listeMois;
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
            ->orWhere('prenom', 'like', '%' . $search . '%')
            ->orWhere('nom', 'like', '%' . $search . '%')
            ->orWhere('permis_numero', 'like', '%' . $search . '%')
            ->limit(25)
            ->get();

        if(!$clients->count()){
            $clients = Client::where('email', 'like', '%' . $search . '%')
                ->orWhere('prenom', 'like', '%' . $search . '%')
                ->orWhere('nom', 'like', '%' . $search . '%')
                ->orWhere('permis_numero', 'like', '%' . $search . '%')
                ->limit(25)
                ->get();
        }

        if(!$clients->count()){
            $clients = Reservation::whereNull('client_id')
                ->where('email', 'like', '%' . $search . '%')
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

    public function getStats($reservationsTransactionQuery, $type_date, $dateDebut, $dateFin)
    {
        $months = $this->getListeMois($dateDebut, $dateFin);
        $stats2['reservation_count'] = [];
        $stats2['montant_total'] = [];


        if ($dateDebut->diffInDays($dateFin) <= 31) {

        }else{
            foreach ($months as $month) {
                $stats2['reservation_count'][$month] = 0;
                $stats2['montant_total'][$month] = 0;
            }
        }

        foreach ($reservationsTransactionQuery as $reservation) {
            if($type_date == 'created_at'){
                $data_month = $reservation->created_at->format('F Y');
                if($dateDebut->diffInDays($dateFin) <= 31){
                    $data_month = $reservation->created_at->format('d/m/Y');
                    if (!isset($stats2['reservation_count'][$data_month])) {
                        $stats2['reservation_count'][$data_month] = 0;
                        $stats2['montant_total'][$data_month] = 0;
                    }
                }
            }else{
                $data_month = $reservation->debut_at->format('F Y');
                if($dateDebut->diffInDays($dateFin) <= 31){
                    $data_month = $reservation->debut_at->format('d/m/Y');
                    if (!isset($stats2['reservation_count'][$data_month])) {
                        $stats2['reservation_count'][$data_month] = 0;
                        $stats2['montant_total'][$data_month] = 0;
                    }
                }
            }
            $stats2['reservation_count'][$data_month] += 1;
            $stats2['montant_total'][$data_month] += $reservation->total;
        }

        $stats['transaction_mois'] = [];
        foreach ($stats2['reservation_count'] as $mois => $count) {
            $stats['transaction_mois'][] = ['mois' => $mois, 'count' => $count, 'montant' => $stats2['montant_total'][$mois]];
        }

        $stats['moisLabels'] = array_column($stats['transaction_mois'], 'mois');
        $stats['reservationCountData'] = array_column($stats['transaction_mois'], 'count');
        $stats['montantTotalData'] = array_column($stats['transaction_mois'], 'montant');
        return $stats;
    }
}
