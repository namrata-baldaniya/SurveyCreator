<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SurveyController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('surveys', SurveyController::class);
Route::post('/surveys/{survey}/submit', [SurveyController::class, 'submit'])->name('surveys.submit');
Route::get('/report/performance', [ReportController::class, 'showReport'])->name('report.performance');
Route::get('/survey/{slug}', [SurveyController::class, 'showBySlug'])->name('surveys.share');
