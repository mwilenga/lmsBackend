<?php

use App\Core\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('module')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [ModuleController::class, 'iget']);
    Route::get('ilist', [ModuleController::class, 'ilist']);
    Route::post('iformAction', [ModuleController::class, 'formAction']);
});