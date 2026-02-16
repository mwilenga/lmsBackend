<?php

use App\Core\Controllers\AssignmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('assignment')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [AssignmentController::class, 'iget']);
    Route::get('ilist', [AssignmentController::class, 'ilist']);
    Route::post('iformAction', [AssignmentController::class, 'formAction']);
});