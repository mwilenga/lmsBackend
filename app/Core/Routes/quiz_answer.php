<?php

use App\Core\Controllers\QuizAnswerController;
use Illuminate\Support\Facades\Route;

Route::prefix('quizAnswer')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [QuizAnswerController::class, 'iget']);
    Route::get('ilist', [QuizAnswerController::class, 'ilist']);
    Route::get('iresults', [QuizAnswerController::class, 'iresults']);
    Route::post('iformAction', [QuizAnswerController::class, 'formAction']);
});