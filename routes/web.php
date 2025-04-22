<?php

use App\Http\Controllers\NutritionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NutritionController::class, 'index'])->name('home');
Route::get('/nutrition/{id}/export-pdf', [NutritionController::class, 'exportPdf'])->name('nutrition.exportPdf');
Route::get('/nutrition/{id}/export-doc', [NutritionController::class, 'exportDoc'])->name('nutrition.exportDoc');
Route::resource('nutrition', NutritionController::class);
