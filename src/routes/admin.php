<?php

use Illuminate\Support\Facades\Route;
use Ipsum\Reservation\app\Http\Controllers\ClientController;

Route::controller(\Ipsum\Reservation\app\Http\Controllers\ReservationController::class)->prefix('reservation')->name('admin.reservation.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::get('export', 'export')->name('export');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{reservation}/destroy', 'destroy')->name('destroy');
        Route::put('{reservation}', 'update')->name('update');
        Route::any('vehicule-select', 'vehiculeSelect')->name('vehiculeSelect');
        Route::any('tarifs/{reservation?}', 'updateTarifs')->name('updateTarifs');
        Route::get('{reservation}/edit', 'edit')->name('edit');
        Route::get('{reservation}/confirmation', 'confirmation')->name('confirmation')->middleware('adminReservationConfirmed');
        Route::get('{reservation}/confirmation-send', 'confirmationSend')->name('confirmationSend')->middleware('adminReservationConfirmed');
        Route::get('{reservation}/contrat', 'contrat')->name('contrat')->middleware('adminReservationConfirmed');
        Route::get('planning', 'planning')->name('planning');
        Route::get('planning/optimiser', 'planningOptimiser')->name('planningOptimiser');
        Route::get('depart-retour', 'departEtRetour')->name('departEtRetour');
        Route::get('contrat-depart/{date?}', 'contratDepart')->name('contratDepart');
    }
);

Route::controller(\Ipsum\Reservation\app\Http\Controllers\PaiementController::class)->prefix('paiement')->name('admin.paiement.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::get('export', 'export')->name('export');
        Route::any('{paiement}/destroy', 'destroy')->name('destroy');
        Route::put('{paiement}', 'update')->name('update');
        Route::get('{paiement}/edit', 'edit')->name('edit');
    }
);


Route::controller(\Ipsum\Reservation\app\Http\Controllers\CategorieController::class)->prefix('categorie')->name('admin.categorie.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{categorie}/destroy', 'destroy')->name('destroy');
        Route::put('{categorie}/{locale?}', 'update')->name('update');
        Route::get('{categorie}/edit/{locale?}', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\CategorieBlocageController::class)->prefix('categorie-blocage')->name('admin.categorieBlocage.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{blocage}/destroy', 'destroy')->name('destroy');
        Route::put('{blocage}', 'update')->name('update');
        Route::get('{blocage}/edit', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\VehiculeController::class)->prefix('vehicule')->name('admin.vehicule.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{vehicule}/destroy', 'destroy')->name('destroy');
        Route::put('{vehicule}', 'update')->name('update');
        Route::get('{vehicule}/edit', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\InterventionController::class)->prefix('intervention')->name('admin.intervention.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{intervention}/destroy', 'destroy')->name('destroy');
        Route::put('{intervention}', 'update')->name('update');
        Route::get('{intervention}/edit', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\CarrosserieController::class)->prefix('carrosserie')->name('admin.carrosserie.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{carrosserie}/destroy', 'destroy')->name('destroy');
        Route::put('{carrosserie}/{locale?}', 'update')->name('update');
        Route::get('{carrosserie}/edit/{locale?}', 'edit')->name('edit');
        Route::any('changeOrder', 'changeOrder')->name('changeOrder');
    }
);


Route::controller(\Ipsum\Reservation\app\Http\Controllers\LieuController::class)->prefix('lieu')->name('admin.lieu.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('horaire/{horaire}', 'updateHoraire')->name('updateHoraire');
        Route::put('{lieu}/horaire', 'storeHoraire')->name('storeHoraire');
        Route::any('horaire/{horaire}/destroy', 'destroyHoraire')->name('destroyHoraire');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{lieu}/destroy', 'destroy')->name('destroy');
        Route::put('{lieu}/{locale?}', 'update')->name('update');
        Route::get('{lieu}/edit/{locale?}', 'edit')->name('edit');
        Route::get('{lieu}/activation', 'activation')->name('activation');
        Route::any('changeOrder', 'changeOrder')->name('changeOrder');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\LieuFermetureController::class)->prefix('lieu-fermeture')->name('admin.lieuFermeture.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{fermeture}/destroy', 'destroy')->name('destroy');
        Route::put('{fermeture}', 'update')->name('update');
        Route::get('{fermeture}/edit', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\LieuFerieController::class)->prefix('lieu-ferie')->name('admin.lieuFerie.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{ferie}/destroy', 'destroy')->name('destroy');
        Route::put('{ferie}', 'update')->name('update');
        Route::get('{ferie}/edit', 'edit')->name('edit');
    }
);


Route::controller(\Ipsum\Reservation\app\Http\Controllers\SaisonController::class)->prefix('saison')->name('admin.saison.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('{saison}/cloner', 'cloner')->name('cloner');
        Route::get('create', 'create')->name('create');
        Route::any('{saison}/destroy', 'destroy')->name('destroy');
        Route::put('{saison}', 'update')->name('update');
        Route::get('{saison}/edit', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\DureeController::class)->prefix('duree')->name('admin.duree.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{duree}/destroy', 'destroy')->name('destroy');
        Route::put('{duree}', 'update')->name('update');
        Route::get('{duree}/edit', 'edit')->name('edit');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\TarifController::class)->prefix('tarif')->name('admin.tarif.')->group(
    function () {
        Route::put('{saison}', 'update')->name('update');
        Route::get('{saison}/edit', 'edit')->name('edit');
    }
);


Route::controller(\Ipsum\Reservation\app\Http\Controllers\PrestationController::class)->prefix('prestation')->name('admin.prestation.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{prestation}/destroy', 'destroy')->name('destroy');
        Route::put('{prestation}/{locale?}', 'update')->name('update');
        Route::get('{prestation}/edit/{locale?}', 'edit')->name('edit');
        Route::any('changeOrder', 'changeOrder')->name('changeOrder');
    }
);
Route::controller(\Ipsum\Reservation\app\Http\Controllers\PrestationBlocageController::class)->prefix('prestation-blocage')->name('admin.prestationBlocage.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{blocage}/destroy', 'destroy')->name('destroy');
        Route::put('{blocage}', 'update')->name('update');
        Route::get('{blocage}/edit', 'edit')->name('edit');
    }
);

Route::controller(\Ipsum\Reservation\app\Http\Controllers\PromotionController::class)->prefix('promotion')->name('admin.promotion.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{promotion}/destroy', 'destroy')->name('destroy');
        Route::put('{promotion}/{locale?}', 'update')->name('update');
        Route::get('{promotion}/edit/{locale?}', 'edit')->name('edit');
        Route::get('{promotion}/desactivation', 'desactivation')->name('desactivation');
    }
);

Route::controller(ClientController::class)->prefix('client')->name('admin.client.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::get('export', 'export')->name('export');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{client}/destroy', 'destroy')->name('destroy');
        Route::put('{client}', 'update')->name('update');
        Route::get('{client}/edit', 'edit')->name('edit');
    }
);
