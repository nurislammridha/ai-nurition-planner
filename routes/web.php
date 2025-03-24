<?php

use App\Http\Controllers\NutritionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NutritionController::class, 'index'])->name('home');
Route::resource('nutrition', NutritionController::class);
