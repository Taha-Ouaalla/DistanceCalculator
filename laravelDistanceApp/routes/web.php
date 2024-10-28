<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DistanceController;

Route::get('/', [DistanceController::class, 'index']);
Route::post('/calculate', [DistanceController::class, 'calculate'])->name('calculate.distance');
Route::post('/save', [DistanceController::class, 'save'])->name('save.distance');
