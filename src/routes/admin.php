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

