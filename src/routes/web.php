<?php
use Illuminate\Support\Facades\Route;


Route::controller(\Ipsum\Reservation\app\Http\Controllers\DevisController::class)->prefix('devis')->name('devis.')->group(
    function () {
        Route::any('{reservation}', 'show')->name('show')->middleware('signed')->middleware('adminDevisCheck')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        Route::get('{reservation}/banque', 'redirectBanque')->name('redirectBanque')->middleware('signed')->middleware('adminDevisCheck');
        Route::any('{reservation}/confirmation', 'confirmation')->name('confirmation')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }
);

Route::post('webhooks/caution/swikly', [\Ipsum\Reservation\app\Http\Controllers\CautionController::class, 'swikly'])
    ->name('caution.webhooks.swikly')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class); // TODO ACTIVER ET VERIFIER