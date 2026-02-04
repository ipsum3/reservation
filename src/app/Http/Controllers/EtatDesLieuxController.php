<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Article\app\Models\Article;
use Ipsum\Reservation\app\Models\Dommage\Dommage;
use Ipsum\Reservation\app\Models\Dommage\Element;
use Ipsum\Reservation\app\Models\Dommage\Emplacement;
use Ipsum\Reservation\app\Models\Inspection\Inspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
use ddn\sapp\PDFDoc;
use Str;

class EtatDesLieuxController extends AdminController
{
    protected $acces = 'vehicule';

    public function index(Request $request)
    {
        $query = Inspection::query()->with(['reservation', 'admin', 'type', 'reservation.vehicule'])
            ->withCount(['dommages'])
            ->whereHas('reservation');

        if ($request->filled('date_debut')) {
            try {
                $date = explode(' - ', $request->get('date_debut'));
                $date1 = Carbon::createFromFormat('d/m/Y', $date[0])->startOfDay();
                $date2 = Carbon::createFromFormat('d/m/Y', $date[1])->endOfDay();

                $query->whereHas('reservation', function ($q) use ($date1, $date2) {
                    $q->whereBetween('debut_at', [$date1, $date2]);
                });
            } catch (\Exception $e) {}
        }
        if ($request->filled('date_fin')) {
            try {
                $date = explode(' - ', $request->get('date_fin'));
                $date1 = Carbon::createFromFormat('d/m/Y', $date[0])->startOfDay();
                $date2 = Carbon::createFromFormat('d/m/Y', $date[1])->endOfDay();

                $query->whereHas('reservation', function ($q) use ($date1, $date2) {
                    $q->whereBetween('fin_at', [$date1, $date2]);
                });
            } catch (\Exception $e) {}
        }

        if ($request->filled('search')) {
            $search = $request->get('search');

            $query->where(function (Builder $query) use ($search) {
                $query->orWhere('id', 'reservation_id', 'like', "%{$search}%")
                    ->orWhere('observations', 'like', "%{$search}%");

                $query->orWhereHas('reservation', function ($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        foreach (['reference', 'contrat', 'nom', 'prenom', 'email', 'telephone', 'immatriculation'] as $colonne) {
                            $q->orWhere($colonne, 'like', '%'.$search.'%');
                        }
                    });
                });
            });
        }

        if ($request->filled('tri')) {
            $query->orderBy($request->tri, $request->order);
        }else{
            $query->orderBy('created_at', 'desc');
        }

        $inspections = $query->paginate();

        return view('IpsumReservation::reservation.etat_des_lieux.index', compact('inspections'));
    }

    /** Récupère l’inspection selon le type */
    public function getInspection(Reservation $reservation, Type $type)
    {
        return $type->id == Type::FINAL_ID
            ? $reservation->inspection_finale
            : $reservation->inspection_initiale;
    }

    /** Met à jour ou crée une inspection */
    protected function updateInspection(array $data, Reservation $reservation, Type $type, $inspection)
    {
        $data['type_id'] = $type->id;
        $data['admin_id'] = auth()->id();


        // TODO pas de creation initiale si déjà une autre inspection

        return $reservation->inspections()->updateOrCreate(
            ['id' => $inspection?->id],
            $data
        );
    }

    /** --------- VÉHICULE --------- */
    public function vehicule(Reservation $reservation, Type $type)
    {
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
            'reservation', 'type', 'vehicules', 'conflicts', 'categories'
        ));
    }

    public function storeVehicule(StoreInspectionVehicule $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $data = $request->validated();
        $this->updateInspection($data, $reservation, $type, $inspection);

        $reservation->update([
            'categorie_id'    => $data['categorie_id'],
            'vehicule_id'     => $data['vehicule_id']
        ]);

        return redirect()->route('admin.inspection.client', [$reservation, $type]);
    }

    /** --------- CLIENT --------- */
    public function client(Reservation $reservation, Type $type)
    {
        if (!$reservation->email ) {
            Alert::error("Email client non renseigné.")->flash();
        }

        $pays = Pays::orderBy('nom')->pluck('nom', 'id');
        $lieux = Lieu::pluck('nom', 'id');

        return view('IpsumReservation::reservation.etat_des_lieux.step.client', compact(
            'reservation', 'type', 'pays', 'lieux'
        ));
    }

    /** --------- CHECKLIST --------- */
    public function checklist(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $checklists = Checklist::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.checklist', compact(
            'reservation', 'inspection', 'type', 'checklists'
        ));
    }

    public function storeChecklist(StoreInspectionChecklist $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $data = $request->validated();
        $inspection = $this->updateInspection($data, $reservation, $type, $inspection);

        $inspection->checklists()->sync($data['checklists'] ?? []);

        return redirect()->route('admin.inspection.dommages', [$reservation, $type]);
    }

    /** --------- DOMMAGE --------- */
    public function dommages(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        return view('IpsumReservation::reservation.etat_des_lieux.step.dommages', compact(
            'reservation', 'inspection', 'type'
        ));
    }

    public function createDommage(Reservation $reservation, Type $type)
    {
        $dommage = new Dommage();
        $inspection = $this->getInspection($reservation, $type);

        $dommage_types       = \Ipsum\Reservation\app\Models\Dommage\Type::orderBy('order')->get();
        $dommage_elements    = Element::orderBy('order')->get();
        $dommage_emplacements = Emplacement::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.dommage', compact('reservation', 'inspection', 'type', 'dommage_types', 'dommage_emplacements', 'dommage_elements', 'dommage'));
    }

    public function storeDommage(StoreInspectionDommage $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $dommageData = $request->validated();
        $dommageData['inspection_id'] = $inspection->id;
        $dommageData['vehicule_id']   = $reservation->vehicule_id;

        $dommage = Dommage::create($dommageData);

        return redirect()->route('admin.inspection.dommages', [$reservation, $type]);
    }

    public function editDommage(Reservation $reservation, Type $type, Dommage $dommage)
    {
        $inspection = $this->getInspection($reservation, $type);

        $dommage_types       = \Ipsum\Reservation\app\Models\Dommage\Type::orderBy('order')->get();
        $dommage_elements    = Element::orderBy('order')->get();
        $dommage_emplacements = Emplacement::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.dommage', compact('reservation', 'inspection', 'type', 'dommage_types', 'dommage_emplacements', 'dommage_elements', 'dommage'));
    }

    public function updateDommage(StoreInspectionDommage $request, Reservation $reservation, Type $type, Dommage $dommage)
    {
        $inspection = $this->getInspection($reservation, $type);

        $dommageData = $request->validated();
        $dommageData['inspection_id'] = $inspection->id;
        $dommageData['vehicule_id']   = $reservation->vehicule_id;

        $dommage->update($dommageData);

        return redirect()->route('admin.inspection.dommages', [$reservation, $type]);
    }

    public function destroyDommage(Reservation $reservation, Type $type, Dommage $dommage)
    {
        $inspection = $this->getInspection($reservation, $type);

        if ($inspection && $inspection->isSigned()) {
            $dommage->delete();
        } else {
            $dommage->forceDelete();
        }

        Alert::success('Le dommage a été supprimé avec succès.')->flash();

        return redirect()->route('admin.inspection.dommages', [$reservation, $type]);
    }


    /** --------- PHOTOS --------- */
    public function photo(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        return view('IpsumReservation::reservation.etat_des_lieux.step.photos', compact(
            'reservation', 'inspection', 'type'
        ));
    }


    /** --------- SIGNATURE --------- */
    public function signatureLocataire(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $checklists = Checklist::orderBy('order')->get();

        $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->first();

        return view('IpsumReservation::reservation.etat_des_lieux.step.signature_locataire', compact(
            'reservation', 'inspection', 'type', 'checklists', 'cgl'
        ));
    }

    public function storeSignatureLocataire(StoreInspectionSignatureLocataire $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $data = $request->validated();
        $data['locataire_signature_at'] = !empty($data['locataire_signature']) ? Carbon::now() : null;

        $this->updateInspection($data, $reservation, $type, $inspection);

        return redirect()->route('admin.inspection.signature.agent', [$reservation, $type]);
    }

    public function signatureAgent(Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        return view('IpsumReservation::reservation.etat_des_lieux.step.signature_agent', compact(
            'reservation', 'inspection', 'type'
        ));
    }

    public function storeSignatureAgent(StoreInspectionSignatureAgent $request, Reservation $reservation, Type $type)
    {
        $inspection = $this->getInspection($reservation, $type);

        $data = $request->validated();
        $data['agent_signature_at'] = Carbon::now();

        $inspection = $this->updateInspection($data, $reservation, $type, $inspection);
        $checklists = Checklist::orderBy('order')->get();

        $emailClient = $reservation->email ?? null;
        if (!$emailClient) {
            Alert::error("Email client non renseigné.")->flash();
            return back();
        }

        try{
            /**
             * Création du dossier inspections s’il n’existe pas
             */
            $directory = storage_path('app/inspections');
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            $inspectionPdfPath = $directory . "/etat_des_lieux-{$inspection->id}.pdf";

            PDF::loadView(config('ipsum.reservation.etat_des_lieux.view'), [
                'reservation' => $reservation,
                'type' => $type,
                'inspection' => $inspection,
                'checklists' => $checklists,
            ])->save($inspectionPdfPath);

            // 🔐 Signature numérique état des lieux
            $this->signPdf($inspectionPdfPath);

            /**
             * SIGNATURE DU CONTRAT (CGL)
             */
            $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->first();

            $contratDirectory = storage_path('app/contrats');
            if (!is_dir($contratDirectory)) {
                mkdir($contratDirectory, 0775, true);
            }

            $contratPdfPath = $contratDirectory . "/contrat-{$reservation->contrat}.pdf";

            Pdf::loadView(
                config('ipsum.reservation.contrat.view'),
                compact('reservation', 'cgl', 'inspection')
            )->save($contratPdfPath);

            // 🔐 Signature numérique contrat
            $this->signPdf($contratPdfPath);

            /**
             * ENVOI DU DOCUMENT FINAL
             */
            Mail::send(new EtatDesLieux($inspection));

        } catch(\Exception $exception) {
            \Log::error("Erreur lors de la signature numérique du PDF : " . $exception->getMessage());

            $data['agent_signature'] = null;
            $data['agent_signature_at'] = null;
            $inspection = $this->updateInspection($data, $reservation, $type, $inspection);

            Alert::error("Inspection #".$inspection->id." : ".$exception->getMessage())->flash();

            return back();
        }


        Alert::success("Document signé et envoyé avec succès")->flash();

        return redirect()->route('admin.inspection.show', [$inspection]);
    }


    public function show(Inspection $inspection)
    {
        $reservation = $inspection->reservation;
        $type = $inspection->type;

        if (!$reservation) {
            Alert::error('Réservation supprimée')->flash();
            return back();
        }

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
        $pdfPath = storage_path("app/inspections/etat_des_lieux-{$inspection->id}.pdf");

        if (!file_exists($pdfPath)) {
            Alert::error('Fichier introuvable')->flash();
            return back();
        }

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="etat-des-lieux-'.$inspection->reservation_id.'-'.Str::slug($inspection->type->nom).'.pdf"',
        ]);
    }

    /**
     * Permet de visualiser le pdf pour tester
     */
    public function showPdf(Inspection $inspection)
    {
        $reservation = $inspection->reservation;
        $type = $inspection->type;
        $checklists = Checklist::orderBy('order')->get();

        $pdf = Pdf::loadView(config('ipsum.reservation.etat_des_lieux.view'), compact('inspection', 'reservation', 'type', 'checklists'));

        return $pdf->stream("etat_des_lieux_{$inspection->id}.pdf");
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

    private function signPdf(string $pdfPath): void
    {
        $fileContent = file_get_contents($pdfPath);
        if ($fileContent === false) {
            throw new \Exception("Impossible de lire le PDF.");
        }

        $pdfDoc = PDFDoc::from_string($fileContent);
        if ($pdfDoc === false) {
            throw new \Exception("Impossible de charger le PDF pour signature.");
        }

        $cert = __DIR__ . '/../../../ressources/certificat/certificat.p12';
        $password = '1234';

        if (!file_exists($cert)) {
            throw new \Exception("Certificat introuvable.");
        }

        if (!$pdfDoc->set_signature_certificate($cert, $password)) {
            throw new \Exception("Certificat invalide.");
        }

        $signedPdf = $pdfDoc->to_pdf_file_s();
        if ($signedPdf === false) {
            throw new \Exception("Erreur lors de la signature numérique.");
        }

        file_put_contents($pdfPath, $signedPdf);
    }
}
