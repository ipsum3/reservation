<?php

use Illuminate\Support\Facades\Route;

Route::controller(\Ipsum\Reservation\app\Http\Controllers\ReservationController::class)->prefix('reservation')->name('admin.reservation.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::get('export', 'export')->name('export');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{reservation}/destroy', 'destroy')->name('destroy');
        Route::put('{reservation}', 'update')->name('update');
        Route::get('{reservation}/edit', 'edit')->name('edit');
        Route::get('{reservation}/confirmation', 'confirmation')->name('confirmation')->middleware('adminReservationConfirmed');
        Route::get('{reservation}/confirmation-send', 'confirmationSend')->name('confirmationSend')->middleware('adminReservationConfirmed');
    }
);


Route::controller(\Ipsum\Reservation\app\Http\Controllers\CategorieController::class)->prefix('categorie')->name('admin.categorie.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{categorie}/destroy', 'destroy')->name('destroy');
        Route::put('{categorie}', 'update')->name('update');
        Route::get('{categorie}/edit', 'edit')->name('edit');
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
Route::controller(\Ipsum\Reservation\app\Http\Controllers\CarrosserieController::class)->prefix('carrosserie')->name('admin.carrosserie.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{carrosserie}/destroy', 'destroy')->name('destroy');
        Route::put('{carrosserie}', 'update')->name('update');
        Route::get('{carrosserie}/edit', 'edit')->name('edit');
        Route::any('changeOrder', 'changeOrder')->name('changeOrder');
    }
);


Route::controller(\Ipsum\Reservation\app\Http\Controllers\LieuController::class)->prefix('lieu')->name('admin.lieu.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{lieu}/destroy', 'destroy')->name('destroy');
        Route::put('{lieu}', 'update')->name('update');
        Route::get('{lieu}/edit', 'edit')->name('edit');
        Route::get('{lieu}/activation', 'activation')->name('activation');
        Route::any('changeOrder', 'changeOrder')->name('changeOrder');
        Route::post('horaire/{horaire}', 'updateHoraire')->name('updateHoraire');
        Route::put('{lieu}/horaire', 'storeHoraire')->name('storeHoraire');
        Route::any('horaire/{horaire}/destroy', 'destroyHoraire')->name('destroyHoraire');
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
        Route::put('{prestation}', 'update')->name('update');
        Route::get('{prestation}/edit', 'edit')->name('edit');
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
