<?php

use App\Core\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::prefix('quiz')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [QuizController::class, 'iget']);
    Route::get('ilist', [QuizController::class, 'ilist']);
    Route::post('iformAction', [QuizController::class, 'formAction']);
});