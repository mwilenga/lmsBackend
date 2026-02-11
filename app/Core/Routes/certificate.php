<?php

use App\Core\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::prefix('certificate')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [CertificateController::class, 'iget']);
    Route::get('ilist', [CertificateController::class, 'ilist']);
    Route::post('iformAction', [CertificateController::class, 'formAction']);
});