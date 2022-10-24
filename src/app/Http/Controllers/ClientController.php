<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Http\Request;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\UpdateClient;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Hash;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class ClientController extends AdminController
{
    protected $acces = 'client';

    protected function query(Request $request)
    {
        $query = Client::query()->withCount('reservations');

        if ($request->filled('search')) {
            $query->where(function($query) use ($request) {
                foreach (['nom', 'prenom', 'email'] as $colonne) {
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

        $clients = $this->query($request)->paginate();

        return view('IpsumReservation::client.index', compact('clients'));
    }

    public function export(Request $request)
    {

        $clients = $this->query($request)->get();

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
            '',
        ];


        $fileName = "export-client-" . date('d-m-Y_H-i-s') . ".csv";

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->setFieldDelimiter(';');
        $writer->setFieldEnclosure('"');
        $writer->openToBrowser($fileName);
        $row = WriterEntityFactory::createRowFromArray($entete);
        $writer->addRow($row);

        foreach ($clients as $client) {

            $data = [
                $client->created_at,
                $client->updated_at,
                $client->email,
                $client->nom,
                $client->prenom,
                $client->telephone,
                $client->adresse,
                $client->cp,
                $client->ville,
                $client->pays ? $client->pays->nom : null,
            ];
            $row = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($row);
        }
        $writer->close();

        return null;
    }

    public function edit(Client $client)
    {
        $pays = Pays::orderBy('nom')->get()->pluck('nom', 'id');
        return view('IpsumReservation::client.form', compact('client', 'pays'));
    }

    public function update(UpdateClient $request, Client $client)
    {
        $client->update($request->except('password'));

        if ($request->filled('password')) {
            $client->password = Hash::make($request->password);
            $client->save();
        }

        Alert::success("L'enregistrement a bien été modifié")->flash();
        return back();
    }

    public function destroy(Client $client)
    {
        if ($client->reservations()->count()) {
            Alert::warning("Impossible de supprimer l'enregistrement, car il existe des réservations associées")->flash();
            return back();
        }

        $client->delete();

        Alert::warning("L'enregistrement a bien été supprimé")->flash();
        return redirect()->route('admin.client.index');

    }
}
