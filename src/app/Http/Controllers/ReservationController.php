<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreAdminReservation;
use Ipsum\Reservation\app\Mail\Confirmation;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Prologue\Alerts\Facades\Alert;

class ReservationController extends AdminController
{
    protected $acces = 'reservation';

    protected function query(Request $request)
    {
        $query = Reservation::with('etat', 'modalite', 'client');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }
        if ($request->filled('etat_id')) {
            $query->where('etat_id', $request->get('etat_id'));
        }
        if ($request->filled('modalite_paiement_id')) {
            $query->where('modalite_paiement_id', $request->get('modalite_paiement_id'));
        }
        if ($request->filled('date_debut')) {
            $query->where('created_at', '>=', $request->get('date_debut'));
        }
        if ($request->filled('date_fin')) {
            $query->where('created_at', '<=', $request->get('date_fin'));
        }
        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['reference', 'nom', 'prenom', 'email', 'telephone'] as $colonne) {
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
        $modalites = Modalite::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.index', compact('reservations', 'etats', 'modalites'));
    }

    public function export(Request $request)
    {

        $reservations = $this->query($request)->get();

        $entete = [
            'Date',
            'Date modification',
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
            'Modalité',
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
                $reservation->modalite ? $reservation->modalite->nom : null,
            ];
            $row = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($row);
        }
        $writer->close();

        return null;
    }

    public function create()
    {
        $reservation = new Reservation;

        $etats = Etat::all()->pluck('nom', 'id');
        $modalites = Modalite::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::all()->pluck('nom', 'id');
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'modalites', 'pays', 'categories', 'lieux'));
    }

    public function store(StoreAdminReservation $request)
    {
        $reservation = Reservation::create($request->validated());
        Alert::success("L'enregistrement a bien été ajouté")->flash();
        return redirect()->route('admin.reservation.edit', $reservation);
    }

    public function edit(Reservation $reservation)
    {
        $etats = Etat::all()->pluck('nom', 'id');
        $modalites = Modalite::all()->pluck('nom', 'id');
        $pays = Pays::all()->pluck('nom', 'id');
        $categories = Categorie::all()->pluck('nom', 'id');
        $lieux = Lieu::orderBy('order')->get()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.form', compact('reservation', 'etats', 'modalites', 'pays', 'categories', 'lieux'));
    }

    public function update(StoreAdminReservation $request, Reservation $reservation)
    {
        $reservation->update($request->validated());

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.reservation.index');

    }

    public function confirmation(Reservation $reservation)
    {
        return view('IpsumReservation::reservation.emails.confirmation', compact('reservation'));
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
}
