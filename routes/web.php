<?php

use App\Http\Controllers\NutritionController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [NutritionController::class, 'index'])->name('home');
// Route::get('/nutrition/{id}/export-pdf', [NutritionController::class, 'exportPdf'])->name('nutrition.exportPdf');
// Route::get('/nutrition/{id}/export-doc', [NutritionController::class, 'exportDoc'])->name('nutrition.exportDoc');
// //for editing plain text generating by chatGpt
// Route::get('/nutrition/{id}/edit-day/{day}', [NutritionController::class, 'editDay'])->name('nutrition.editDay');
// Route::post('/nutrition/{id}/edit-day/{day}', [NutritionController::class, 'updateDay'])->name('nutrition.updateDay');
// Route::resource('nutrition', NutritionController::class);
// Route::resource('workout', WorkoutController::class);
// Nutrition Routes
Route::get('/', [NutritionController::class, 'index'])->name('home');
Route::get('/nutrition/{id}/export-pdf', [NutritionController::class, 'exportPdf'])->name('nutrition.exportPdf');
Route::get('/nutrition/{id}/export-doc', [NutritionController::class, 'exportDoc'])->name('nutrition.exportDoc');
Route::get('/nutrition/{id}/edit-day/{day}', [NutritionController::class, 'editDay'])->name('nutrition.editDay');
Route::post('/nutrition/{id}/edit-day/{day}', [NutritionController::class, 'updateDay'])->name('nutrition.updateDay');
Route::resource('nutrition', NutritionController::class);

// Workout Routes
Route::get('/workout/{id}/export-pdf', [WorkoutController::class, 'exportPdf'])->name('workout.exportPdf');
Route::get('/workout/{id}/export-doc', [WorkoutController::class, 'exportDoc'])->name('workout.exportDoc');
Route::get('/workout/{id}/edit-day/{day}', [WorkoutController::class, 'editDay'])->name('workout.editDay');
Route::post('/workout/{id}/edit-day/{day}', [WorkoutController::class, 'updateDay'])->name('workout.updateDay');
Route::resource('workout', WorkoutController::class);
