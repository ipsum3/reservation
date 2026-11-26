<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Ipsum\Article\app\Models\Article;
use Ipsum\Reservation\app\Mail\NewDommage;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
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
use Ipsum\Reservation\app\Rules\VehiculeDisponible;
use Prologue\Alerts\Facades\Alert;
use ddn\sapp\PDFDoc;
use Illuminate\Support\Facades\Validator;

class EtatDesLieuxController extends AdminController
{
    protected $acces = 'vehicule';

    public function index(Request $request)
    {
        $query = Inspection::query()->with(['admin', 'type', 'reservation.vehicule'])
            ->withCount(['dommages'])
            ->whereHas('reservation');

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->get('type_id'));
        }

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

        $types = Type::all()->pluck('nom', 'id');

        return view('IpsumReservation::reservation.etat_des_lieux.index', compact('inspections', 'types'));
    }


    public function start(Reservation $reservation, Type $type)
    {
        $inspection = $type->is_final ? $reservation->inspectionFinale : $reservation->inspectionInitiale;

        if ($inspection === null) {
            $inspection = Inspection::create([
               'reservation_id' => $reservation->id,
               'type_id' => $type->id,
               'vehicule_id' => $reservation->vehicule_id,
               'immatriculation' => $reservation->vehicule?->immatriculation,
               'admin_id' => auth()->id(),
            ]);
        }

        // Recherche du véhicule à chaque fois dans le cas ou on créé d'abord l'état des lieux final avant l'initial et ensuite on change le véhicule dans l'initial ou dans la résa

        // Dans le cas d'un changement de véhicule, il faudrait modifier le km et la jauge ainsi que les dommages ? Valable aussi quand on fait la manip manuellement dans etapes vehicules

        if ($type->is_final) {
            $inspection_initial = $reservation->inspectionInitiale()->signed()->first();
            if ($inspection_initial) {
                $inspection->vehicule_id = $inspection_initial->vehicule_id;
                $inspection->immatriculation = $inspection_initial->immatriculation;
                $inspection->save();
            }
        }

        return redirect()->route((!$type->is_final or $inspection->vehicule_id === null) ? 'admin.inspection.vehicule' : 'admin.inspection.checklist', [$inspection]);
    }

    /** --------- VÉHICULE --------- */
    public function vehicule(Inspection $inspection)
    {
        $vehicules = $inspection->reservation->categorie
            ? $inspection->reservation->categorie->vehicules()
                ->with('categorie')
                ->withCountReservationConfirmed($inspection->reservation->debut_at, $inspection->reservation->fin_at)
                ->withCountIntervention($inspection->reservation->debut_at, $inspection->reservation->fin_at)
                ->duParc($inspection->reservation->debut_at, $inspection->reservation->fin_at)
                ->orderBy('mise_en_circualtion_at', 'desc')
                ->get()
            : collect();

        $conflicts = $inspection->reservation->vehicule
            ? $inspection->reservation->vehicule->getConflicts($inspection->reservation)
            : collect();

        $categories = Categorie::orderBy('nom')->pluck('nom', 'id');

        return view('IpsumReservation::reservation.etat_des_lieux.step.vehicule', compact(
            'inspection', 'vehicules', 'conflicts', 'categories'
        ));
    }

    public function storeVehicule(StoreInspectionVehicule $request, Inspection $inspection)
    {
        $vehicule = Vehicule::findOrFail($request->vehicule_id);
        $data = $request->validated();
        $data['immatriculation'] = $vehicule->immatriculation;

        $inspection->update($data);

        if (config('ipsum.reservation.sync_vehicule_entre_reservation_etat_des_lieux') and $inspection->reservation->vehicule_id !== $request->vehicule_id) {

            $inspection->reservation->update([
                'categorie_id'    => $request->categorie_id,
                'vehicule_id'     => $vehicule->id
            ]);

            $validator = Validator::make($request->all(), [
                'vehicule_id' => new VehiculeDisponible($inspection->reservation)
            ]);

            if ($validator->fails()) {
                return back();
            }
        }

        return redirect()->route('admin.inspection.client', [$inspection]);
    }

    /** --------- CLIENT --------- */
    public function client(Inspection $inspection)
    {
        if (!$inspection->reservation->email ) {
            Alert::error("Email client non renseigné.")->flash();
        }

        $pays = Pays::orderBy('nom')->pluck('nom', 'id');
        $lieux = Lieu::pluck('nom', 'id');

        return view('IpsumReservation::reservation.etat_des_lieux.step.client', compact(
            'inspection', 'pays', 'lieux'
        ));
    }

    /** --------- CHECKLIST --------- */
    public function checklist(Inspection $inspection)
    {
        $checklists = Checklist::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.checklist', compact(
            'inspection', 'checklists'
        ));
    }

    public function storeChecklist(StoreInspectionChecklist $request, Inspection $inspection)
    {
        $inspection->update($request->validated());

        $inspection->checklists()->sync($request->checklists ?? []);

        return redirect()->route('admin.inspection.dommages', [$inspection]);
    }

    /** --------- DOMMAGE --------- */
    public function dommages(Inspection $inspection)
    {
        return view('IpsumReservation::reservation.etat_des_lieux.step.dommages', compact(
            'inspection'
        ));
    }

    public function createDommage(Inspection $inspection)
    {
        $dommage = new Dommage();

        $dommage_types       = \Ipsum\Reservation\app\Models\Dommage\Type::orderBy('order')->get();
        $dommage_elements    = Element::orderBy('order')->get();
        $dommage_emplacements = Emplacement::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.dommage', compact('inspection', 'dommage_types', 'dommage_emplacements', 'dommage_elements', 'dommage'));
    }

    public function storeDommage(StoreInspectionDommage $request, Inspection $inspection)
    {
        $dommageData = $request->validated();
        $dommageData['inspection_id'] = $inspection->id;
        $dommageData['vehicule_id']   = $inspection->vehicule_id;

        Dommage::create($dommageData);

        return redirect()->route('admin.inspection.dommages', [$inspection]);
    }

    public function editDommage(Inspection $inspection, Dommage $dommage)
    {
        $dommage_types       = \Ipsum\Reservation\app\Models\Dommage\Type::orderBy('order')->get();
        $dommage_elements    = Element::orderBy('order')->get();
        $dommage_emplacements = Emplacement::orderBy('order')->get();

        return view('IpsumReservation::reservation.etat_des_lieux.step.dommage', compact('inspection', 'dommage_types', 'dommage_emplacements', 'dommage_elements', 'dommage'));
    }

    public function updateDommage(StoreInspectionDommage $request, Inspection $inspection, Dommage $dommage)
    {
        $dommage->update($request->validated());

        return redirect()->route('admin.inspection.dommages', [$inspection]);
    }

    public function destroyDommage(Inspection $inspection, Dommage $dommage)
    {
        if ($inspection && $inspection->isSigned()) {
            $dommage->delete();
        } else {
            $dommage->forceDelete();
        }

        Alert::success('Le dommage a été supprimé avec succès.')->flash();

        return redirect()->route('admin.inspection.dommages', [$inspection]);
    }


    /** --------- PHOTOS --------- */
    public function photo(Inspection $inspection)
    {
        return view('IpsumReservation::reservation.etat_des_lieux.step.photos', compact(
            'inspection'
        ));
    }


    /** --------- SIGNATURE --------- */
    public function signatureLocataire(Inspection $inspection)
    {
        $checklists = Checklist::orderBy('order')->get();

        $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->first();

        return view('IpsumReservation::reservation.etat_des_lieux.step.signature_locataire', compact(
            'inspection', 'checklists', 'cgl'
        ));
    }

    public function storeSignatureLocataire(StoreInspectionSignatureLocataire $request, Inspection $inspection)
    {
        $data = $request->validated();
        $data['locataire_signature_at'] = !empty($data['locataire_signature']) ? Carbon::now() : null;

        $inspection->update($data);

        return redirect()->route('admin.inspection.signature.agent', [$inspection]);
    }

    public function signatureAgent(Inspection $inspection)
    {
        return view('IpsumReservation::reservation.etat_des_lieux.step.signature_agent', compact(
            'inspection'
        ));
    }

    public function storeSignatureAgent(StoreInspectionSignatureAgent $request, Inspection $inspection)
    {
        $reservation = $inspection->reservation;

        $data = $request->validated();
        $data['agent_signature_at'] = Carbon::now();
        $data['admin_id'] = auth()->id();

        $inspection->update($data);

        $checklists = Checklist::orderBy('order')->get();

        try {
            /**
             * Création du dossier inspections s’il n’existe pas
             */
            $directory = storage_path('app/inspections');
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
                }
            }

            $inspectionPdfPath = $directory . "/etat_des_lieux-{$inspection->id}.pdf";

            Pdf::setOption(['isRemoteEnabled' => true, 'chroot' => '/'])->loadView(config('ipsum.reservation.etat_des_lieux.view'), [
                'inspection' => $inspection,
                'checklists' => $checklists,
            ])->save($inspectionPdfPath);

            // 🔐 Signature numérique état des lieux
            $this->signPdf($inspectionPdfPath);

            /**
             * SIGNATURE DU CONTRAT (CGL)
             */
            if ($inspection->type->is_initial) {
                $cgl = Article::where('nom', config('ipsum.reservation.contrat.cgl_nom'))->first();

                $contratDirectory = storage_path('app/contrats');
                if (!is_dir($contratDirectory)) {
                    if (!mkdir($contratDirectory, 0775, true) && !is_dir($contratDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $contratDirectory));
                    }
                }

                $contratPdfPath = $contratDirectory . "/contrat-{$reservation->contrat}.pdf";

                Pdf::setOption(['isRemoteEnabled' => true, 'chroot' => '/'])->loadView(
                    config('ipsum.reservation.contrat.view'),
                    compact('reservation', 'cgl', 'inspection')
                )->save($contratPdfPath);

                // 🔐 Signature numérique contrat
                $this->signPdf($contratPdfPath);
            }

            /**
             * ENVOI DU DOCUMENT FINAL
             */
            Mail::send(new EtatDesLieux($inspection));


            if (config('settings.reservation.email_alerte_dommage') and $inspection->type->is_final and $inspection->dommages->count()) {
                Mail::send(new NewDommage($inspection));
            }

        } catch(\Exception $exception) {
            \Log::error("Erreur lors de la signature numérique du PDF : " . $exception->getMessage());

            $data['agent_signature'] = null;
            $data['agent_signature_at'] = null;
            $inspection->update($data);

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
        if (!file_exists($inspection->document_path)) {
            Alert::error('Fichier introuvable')->flash();
            return back();
        }

        return response()->file($inspection->document_path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$inspection->document_public_file_name.'"',
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

        $pdf = Pdf::setOption(['isRemoteEnabled' => true, 'chroot' => '/'])->loadView(config('ipsum.reservation.etat_des_lieux.view'), compact('inspection', 'reservation', 'type', 'checklists'));

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

        return back();

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
