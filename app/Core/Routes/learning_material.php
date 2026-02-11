<?php

use App\Core\Controllers\LearningMaterialController;
use Illuminate\Support\Facades\Route;

Route::prefix('learningMaterial')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('iget', [LearningMaterialController::class, 'iget']);
    Route::get('ilist', [LearningMaterialController::class, 'ilist']);
    Route::post('iformAction', [LearningMaterialController::class, 'formAction']);
});