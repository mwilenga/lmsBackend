<?php

use App\Core\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [UserController::class, 'iget']);
    Route::get('ilist', [UserController::class, 'ilist']);
    Route::post('iformAction', [UserController::class, 'formAction']);
});