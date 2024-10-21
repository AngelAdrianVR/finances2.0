<?php

use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RecurringIncomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


// Income routes -------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
Route::resource('incomes', IncomeController::class)->middleware('auth');
Route::post('incomes/massive-delete', [IncomeController::class, 'massiveDelete'])->name('incomes.massive-delete');


// Recurring Income routes -------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
Route::resource('recurring-incomes', RecurringIncomeController::class)->middleware('auth');
Route::post('recurring-incomes/massive-delete', [RecurringIncomeController::class, 'massiveDelete'])->name('recurring-incomes.massive-delete');


// Outcome routes -------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
Route::resource('outcomes', OutcomeController::class)->middleware('auth');


// Loan routes ---------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
Route::resource('loans', LoanController::class)->middleware('auth');


// Payment routes -------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
Route::resource('payments', PaymentController::class)->middleware('auth');
