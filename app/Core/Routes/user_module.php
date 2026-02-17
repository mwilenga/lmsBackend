<?php

use App\Core\Controllers\UserModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('userModule')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [UserModuleController::class, 'iget']);
    Route::get('ilist', [UserModuleController::class, 'ilist']);
    Route::post('iformAction', [UserModuleController::class, 'formAction']);
});