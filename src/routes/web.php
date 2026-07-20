<?php
use Illuminate\Support\Facades\Route;


Route::controller(\Ipsum\Reservation\app\Http\Controllers\DevisController::class)->prefix('devis')->name('devis.')->group(
    function () {
        Route::any('{reservation}', 'show')->name('show')->middleware('signed')->middleware('adminDevisCheck')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        Route::get('{reservation}/banque', 'redirectBanque')->name('redirectBanque')->middleware('signed')->middleware('adminDevisCheck');
        Route::any('{reservation}/confirmation', 'confirmation')->name('confirmation')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }
);
