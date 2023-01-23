<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Alert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\UpdatePaiement;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Paiement;
use Ipsum\Reservation\app\Models\Reservation\Type;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;


class PaiementController extends AdminController
{
    protected $acces = 'comptabilite';

    protected function query(Request $request)
    {
        $query = Paiement::with('moyen', 'type', 'reservation.client');

        if ($request->filled('paiement_moyen_id')) {
            $query->where('paiement_moyen_id', $request->get('paiement_moyen_id'));
        }
        if ($request->filled('paiement_type_id')) {
            $query->where('paiement_type_id', $request->get('paiement_type_id'));
        }
        if ($request->filled('reservation_id')) {
            $query->where('reservation_id', $request->get('reservation_id'));
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
                $query->whereHas('reservations', function ($query) use($date1, $date2) {
                    $query->whereBetween('debut_at', [$date1, $date2]);
                });
            } catch (\Exception $e) {}
        }
        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['id', 'montant', 'transaction_ref', 'autorisation_ref'] as $colonne) {
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

        $paiements = $this->query($request)->paginate();

        $moyens = Moyen::all()->pluck('nom', 'id');
        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.paiement.index', compact('paiements', 'moyens', 'types'));
    }

    public function export(Request $request)
    {

        $paiements = $this->query($request)->get();

        $entete = [
            'Réfèrence',
            'Date',
            'Date de départ',
            'Réservation',
            'Contrat',
            'Code client',
            'Nom',
            'Prénom',
            'Moyen',
            'Type',
            'Montant',
        ];


        $fileName = "export-paiement-" . date('d-m-Y_H-i-s') . ".xlsx";

        $writer = new Writer();
        $writer->openToBrowser($fileName);
        $row = Row::fromValues($entete);
        $writer->addRow($row);

        foreach ($paiements as $paiement) {

            $data = [
                $paiement->id,
                $paiement->created_at->format('Y-m-d H:i:s'),
                $paiement->reservation ? ($paiement->reservation->debut_at ? $paiement->reservation->debut_at->format('Y-m-d') : null) : null,
                $paiement->reservation ? $paiement->reservation->reference : null,
                $paiement->reservation ? $paiement->reservation->contrat : null,
                $paiement->reservation ? $paiement->reservation->client_id : null,
                $paiement->reservation ? $paiement->reservation->nom : null,
                $paiement->reservation ? $paiement->reservation->prenom : null,
                $paiement->moyen ? $paiement->moyen->nom : null,
                $paiement->type ? $paiement->type->nom : null,
                (float) $paiement->montant,
            ];
            $row = Row::fromValues($data);
            $writer->addRow($row);

        }

        $row = new Row([Cell::fromValue('')]);
        $writer->addRow($row);
        $row = new Row([Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue(''), Cell::fromValue('Total'), Cell::fromValue('=SUM(K2:K'.(count($paiements) + 1).')')]);
        $writer->addRow($row);
        $writer->close();

        return null;
    }

    public function edit(Paiement $paiement)
    {
        $moyens = Moyen::all();
        $types = Type::all();
        return view('IpsumReservation::reservation.paiement.form', compact('paiement', 'moyens', 'types'));
    }

    public function update(UpdatePaiement $request, Paiement $paiement)
    {
        $paiement->fill($request->validated())->save();

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }


    public function destroy(Paiement $paiement)
    {
        $paiement->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->back();

    }
}
