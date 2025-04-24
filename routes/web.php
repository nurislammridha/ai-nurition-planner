<?php

use App\Http\Controllers\NutritionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NutritionController::class, 'index'])->name('home');
Route::get('/nutrition/{id}/export-pdf', [NutritionController::class, 'exportPdf'])->name('nutrition.exportPdf');
Route::get('/nutrition/{id}/export-doc', [NutritionController::class, 'exportDoc'])->name('nutrition.exportDoc');
//for editing plain text generating by chatGpt
Route::get('/nutrition/{id}/edit-day/{day}', [NutritionController::class, 'editDay'])->name('nutrition.editDay');
Route::post('/nutrition/{id}/edit-day/{day}', [NutritionController::class, 'updateDay'])->name('nutrition.updateDay');
Route::resource('nutrition', NutritionController::class);
