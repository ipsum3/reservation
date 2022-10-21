<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Alert;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Paiement;


class PaiementController extends AdminController
{
    protected $acces = 'comptabilite';

    protected function query(Request $request)
    {
        $query = Paiement::with('moyen', 'reservation.client');

        if ($request->filled('paiement_moyen_id')) {
            $query->where('paiement_moyen_id', $request->get('paiement_moyen_id'));
        }
        if ($request->filled('reservation_id')) {
            $query->where('reservation_id', $request->get('reservation_id'));
        }
        /*if ($request->filled('created_at')) {
            $query->where('created_at', $request->get('created_at'));
        }*/
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

        return view('IpsumReservation::reservation.paiement.index', compact('paiements', 'moyens'));
    }

    public function export(Request $request)
    {

        $paiements = $this->query($request)->get();

        $entete = [
            'Réfèrence',
            'Date',
            'Réservation',
            'Contrat',
            'Nom',
            'Prénom',
            'Moyen',
            'Montant',
        ];


        $fileName = "export-paiement-" . date('d-m-Y_H-i-s') . ".csv";

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->setFieldDelimiter(';');
        $writer->setFieldEnclosure('"');
        $writer->openToBrowser($fileName);
        $row = WriterEntityFactory::createRowFromArray($entete);
        $writer->addRow($row);

        foreach ($paiements as $paiement) {

            $data = [
                $paiement->id,
                $paiement->created_at,
                $paiement->reservation ? $paiement->reservation->reference : null,
                $paiement->reservation ? $paiement->reservation->contrat : null,
                $paiement->reservation ? $paiement->reservation->nom : null,
                $paiement->reservation ? $paiement->reservation->prenom : null,
                $paiement->moyen ? $paiement->moyen->nom : null,
                $paiement->montant,
            ];
            $row = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($row);
        }
        $writer->close();

        return null;
    }


    public function destroy(Paiement $paiement)
    {
        $paiement->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.paiement.index');

    }
}
