<?php

use Illuminate\Support\Facades\Route;


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


Route::controller(\Ipsum\Reservation\app\Http\Controllers\LieuController::class)->prefix('lieu')->name('admin.lieu.')->group(
    function () {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('create', 'create')->name('create');
        Route::any('{categorie}/destroy', 'destroy')->name('destroy');
        Route::put('{categorie}', 'update')->name('update');
        Route::get('{categorie}/edit', 'edit')->name('edit');
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

