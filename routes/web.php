<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RecurringIncomeController;
use App\Http\Controllers\RecurringOutcomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::redirect('/', 'login');

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
Route::post('incomes/get-matches', [IncomeController::class, 'getMatches'])->name('incomes.get-matches');


// Recurring Income routes -------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
Route::resource('recurring-incomes', RecurringIncomeController::class)->middleware('auth');
Route::post('recurring-incomes/massive-delete', [RecurringIncomeController::class, 'massiveDelete'])->name('recurring-incomes.massive-delete');
Route::post('recurring-incomes/get-matches', [RecurringIncomeController::class, 'getMatches'])->name('recurring-incomes.get-matches');
Route::get('recurring-incomes/toggle-status/{recurring_income}', [RecurringIncomeController::class, 'toggleStatus'])->name('recurring-incomes.toggle-status');


// Outcome routes -------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
Route::resource('outcomes', OutcomeController::class)->middleware('auth');
Route::post('outcomes/massive-delete', [OutcomeController::class, 'massiveDelete'])->name('outcomes.massive-delete');
Route::post('outcomes/get-matches', [OutcomeController::class, 'getMatches'])->name('outcomes.get-matches');


// Recurring Outcome routes -------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
Route::resource('recurring-outcomes', RecurringOutcomeController::class)->middleware('auth');
Route::post('recurring-outcomes/massive-delete', [RecurringOutcomeController::class, 'massiveDelete'])->name('recurring-outcomes.massive-delete');
Route::post('recurring-outcomes/get-matches', [RecurringOutcomeController::class, 'getMatches'])->name('recurring-outcomes.get-matches');
Route::get('recurring-outcomes/toggle-status/{recurring_outcome}', [RecurringOutcomeController::class, 'toggleStatus'])->name('recurring-outcomes.toggle-status');


// Loan routes ---------------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------------------------
Route::resource('loans', LoanController::class)->middleware('auth');
Route::post('loans/massive-delete', [LoanController::class, 'massiveDelete'])->name('loans.massive-delete');
Route::post('loans/get-matches', [LoanController::class, 'getMatches'])->name('loans.get-matches');


// Payment routes -------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
Route::resource('payments', PaymentController::class)->middleware('auth');


// calendar routes -------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------
Route::resource('calendars', CalendarController::class)->middleware('auth');
