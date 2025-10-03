<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Reservation\app\Models\Inspection\Inspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ipsum\Admin\app\Http\Controllers\AdminController;
use Ipsum\Reservation\app\Http\Requests\StoreInspectionChecklist;
use Ipsum\Reservation\app\Http\Requests\StoreInspectionDommage;
use Ipsum\Reservation\app\Http\Requests\StoreInspectionSignatureAgent;
use Ipsum\Reservation\app\Http\Requests\StoreInspectionSignatureLocataire;
use Ipsum\Reservation\app\Http\Requests\StoreInspectionVehicule;
use Ipsum\Reservation\app\Mail\EtatDesLieux;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Inspection\Checklist;
use Ipsum\Reservation\app\Models\Inspection\Type;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Prologue\Alerts\Facades\Alert;

class EtatDesLieuxController extends AdminController
{
    protected $acces = 'vehicule';

    public function index(Request $request)
    {
        $query = Inspection::query();

        if ($request->filled('search')) {
            $query->where(function(Builder $query) use ($request) {
                foreach (['nom', 'texte', 'extrait'] as $colonne) {
                    $query->orWhere($colonne, 'like', '%'.$request->get('search').'%');
                }
            });
        }

        $inspections = $query->orderBy('created_at', 'desc')->paginate();

        return view('IpsumReservation::reservation.etat_des_lieux.index', compact('inspections'));
    }

    /** Récupère l’inspection selon le type */
    public function getInspection(Reservation $reservation, Type $type)
    {
        return $type->id == Type::FINAL_ID
            ? $reservation->inspection_finale
            : $reservation->inspection_initiale;
    }

    /** Redirige si inspection déjà signée */
    protected function redirectIfSigned($inspection, Reservation $reservation, Type $type)
    {
        if ($inspection?->isSigned()) {
            return redirect()->route('admin.inspection.show', [$reservation, $type]);
        }
        return null;
    }

    /** Met à jour ou crée une inspection */
    protected function updateInspection(array $data, Reservation $reservation, Type $type, $inspection)
    {
        $data['type_id'] = $type->id;
        $data['admin_id'] = auth()->id();

        return $reservation->inspections()->updateOrCreate(
            ['id' => $inspection?->id],
            $data
        );
    }

    /** --------- VÉHICULE --------- */
    public function vehicule(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        $vehicules = $reservation->categorie
            ? $reservation->categorie->vehicules()
                ->with('categorie')
                ->withCountReservationConfirmed($reservation->debut_at, $reservation->fin_at)
                ->withCountIntervention($reservation->debut_at, $reservation->fin_at)
                ->duParc($reservation->debut_at, $reservation->fin_at)
                ->orderBy('mise_en_circualtion_at', 'desc')
                ->get()
            : collect();

        $conflicts = $reservation->vehicule
            ? $reservation->vehicule->getConflicts($reservation)
            : collect();

        $categories = Categorie::orderBy('nom')->pluck('nom', 'id');

        return view('IpsumReservation::reservation.etat_des_lieux.step.vehicule', compact(
            'reservation', 'inspection', 'type', 'vehicules', 'conflicts', 'categories'
        ));
    }

    public function storeVehicule(StoreInspectionVehicule $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($inspection?->isSigned()) {
            Alert::error('Le document est déjà signé')->flash();
            return back();
        }

        $data = $request->validated();
        $this->updateInspection($data, $reservation, $type, $inspection);

        $reservation->update([
            'categorie_id'    => $data['categorie_id'],
            'vehicule_id'     => $data['vehicule_id'],
            'immatriculation' => $reservation->vehicule?->immatriculation ?? $reservation->immatriculation,
        ]);

        return redirect()->route('admin.inspection.client', [$reservation, $type]);
    }

    /** --------- CLIENT --------- */
    public function client(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        $pays = Pays::orderBy('nom')->pluck('nom', 'id');
        $lieux = Lieu::pluck('nom', 'id');

        return view('IpsumReservation::reservation.etat_des_lieux.step.client', compact(
            'reservation', 'inspection', 'type', 'pays', 'lieux'
        ));
    }

    /** --------- CHECKLIST --------- */
    public function checklist(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        $checklists = Checklist::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.checklist', compact(
            'reservation', 'inspection', 'type', 'checklists'
        ));
    }

    public function storeChecklist(StoreInspectionChecklist $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($inspection?->isSigned()) {
            Alert::error('Le document est déjà signé')->flash();
            return back();
        }

        $data = $request->validated();
        $inspection = $this->updateInspection($data, $reservation, $type, $inspection);

        $inspection->checklists()->sync($data['checklists'] ?? []);

        return redirect()->route('admin.inspection.dommage', [$reservation, $type]);
    }

    /** --------- DOMMAGE --------- */
    public function dommage(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        $dommage_types       = \Ipsum\Reservation\app\Models\Dommage\Type::all();
        $dommage_elements    = \Ipsum\Reservation\app\Models\Dommage\Element::all();
        $dommage_emplacements= \Ipsum\Reservation\app\Models\Dommage\Emplacement::all();

        return view('IpsumReservation::reservation.etat_des_lieux.step.dommage', compact(
            'reservation', 'inspection', 'type', 'dommage_types', 'dommage_elements', 'dommage_emplacements'
        ));
    }

    public function storeDommage(StoreInspectionDommage $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($inspection?->isSigned()) {
            Alert::error('Le document est déjà signé')->flash();
            return back();
        }

        $data = $request->validated();
        $inspection = $this->updateInspection($data, $reservation, $type, $inspection);

        $dommageIds = $data['ids'] ?? [];

        $dommageRelation = $type->id == Type::INITIAL_ID
            ? $reservation->vehicule->dommages()
            : $inspection->dommages();

        $dommageRelation->whereNotIn('id', $dommageIds)->delete();

        foreach ($data['dommages'] ?? [] as $dommageData) {
            $dommageData['inspection_id'] = $inspection->id;
            $dommageData['vehicule_id']   = $reservation->vehicule_id;

            $dommageRelation = $type->id == Type::INITIAL_ID
                ? $reservation->vehicule->dommages()
                : $inspection->dommages();

            $dommage = $dommageRelation->updateOrCreate(
                ['id' => $dommageData['id'] ?? null],
                $dommageData
            );

            if (!empty($dommageData['uuid'])) {
                $inspection->medias()->groupe($dommageData['uuid'])->first()?->update(['groupe' => $dommage->id]);
            }
        }

        return redirect()->route('admin.inspection.photos', [$reservation, $type]);
    }

    /** --------- PHOTOS --------- */
    public function photos(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        return view('IpsumReservation::reservation.etat_des_lieux.step.photos', compact(
            'reservation', 'inspection', 'type'
        ));
    }

    /** --------- RÉCAP --------- */
    public function recapitulatif(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        $checklists = Checklist::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.recapitulatif', compact(
            'reservation', 'inspection', 'type', 'checklists'
        ));
    }

    /** --------- SIGNATURE --------- */
    public function signatureLocataire(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        return view('IpsumReservation::reservation.etat_des_lieux.step.signature_locataire', compact(
            'reservation', 'inspection', 'type'
        ));
    }

    public function storeSignatureLocataire(StoreInspectionSignatureLocataire $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($inspection?->isSigned()) {
            Alert::error('Le document est déjà signé')->flash();
            return back();
        }

        $data = $request->validated();
        $data['locataire_signature_at'] = !empty($data['locataire_signature']) ? Carbon::now() : null;

        $this->updateInspection($data, $reservation, $type, $inspection);

        return redirect()->route('admin.inspection.signature.agent', [$reservation, $type]);
    }

    public function signatureAgent(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($redirect = $this->redirectIfSigned($inspection, $reservation, $type)) return $redirect;

        return view('IpsumReservation::reservation.etat_des_lieux.step.signature_agent', compact(
            'reservation', 'inspection', 'type'
        ));
    }

    public function storeSignatureAgent(StoreInspectionSignatureAgent $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if ($inspection?->isSigned()) {
            Alert::error('Le document est déjà signé')->flash();
            return back();
        }

        $data = $request->validated();
        $data['agent_signature_at'] = !empty($data['agent_signature']) ? Carbon::now() : null;

        $inspection = $this->updateInspection($data, $reservation, $type, $inspection);
        $checklists = Checklist::orderBy('order')->get();

        $emailClient = $reservation->email ?? null;
        if (!$emailClient) {
            Alert::error("Email client non renseigné.")->flash();
            return back();
        }

        try{
            $pdfPath = storage_path("app/inspections/etat_des_lieux-{$reservation->id}-{$type->id}.pdf");
            $pdf = \PDF::loadView(config('ipsum.reservation.etat_des_lieux.view'), [
                'reservation' => $reservation,
                'type' => $type,
                'inspection' => $inspection,
                'checklists' => $checklists,
            ]);
            $pdf->save($pdfPath);

            Mail::send(new EtatDesLieux($reservation, $type, $inspection));
        }catch(\Exception $exception){

        }

        return redirect()->route('admin.inspection.show', [$reservation, $type]);
    }


    public function show(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);
        if (!$inspection->isSigned()) {
            Alert::error('Signature manquante')->flash();
            return back();
        }
        $checklists = Checklist::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.show', compact(
            'reservation', 'inspection', 'type', 'checklists'
        ));
    }

    public function pdf(Inspection $inspection)
    {
        $reservation = $inspection->reservation;
        $type = $inspection->type;

        $pdfPath = storage_path("app/inspections/etat_des_lieux-{$reservation->id}-{$type->id}.pdf");

        if (!file_exists($pdfPath)) {
            Alert::error('Fichier introuvable')->flash();
            return back();
        }

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="etat_des_lieux-'.$reservation->id.'-'.$type->id.'.pdf"',
        ]);
    }

    public function destroy(Inspection $inspection)
    {

        if( $inspection->isSigned() ) {
            Alert::error("Impossible de supprimer cette état des lieux car il est signé.")->flash();
            return back();
        }

        $inspection->delete();
        Alert::warning("L'enregistrement a bien été supprimé")->flash();

        return redirect()->route('admin.inspection.index');

    }
}
