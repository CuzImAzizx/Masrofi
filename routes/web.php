<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Transaction;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/home');
});
Route::get('/terms', [UserController::class, 'displayTerms']);

Route::get('/dashboard', function () {
    //return view('dashboard');
    return redirect('/home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/terms/accept', [UserController::class, 'acceptTerms']);
    Route::get('/terms/deny', [UserController::class, 'denyTerms']);

});

Route::middleware(['auth', 'TermsAccepted'])->group(function () {

    Route::get('/home', [UserController::class, 'showHomePage']);

    Route::get('/addTransaction', [UserController::class, 'showAddTransactionPage']);
    Route::post('/addTransaction', [UserController::class, 'analyzeTransaction']);
    
    Route::get('/addTransactionManual', [UserController::class, 'showAddTransactionManualPage']);

    Route::post('/insertTransaction', [UserController::class, 'insertTransaction']);

    Route::get('/transactions', [UserController::class, 'viewTransactionsThisMonth']);
    Route::post('/transactions', [UserController::class, 'filterTransactions']);

    Route::get('/transactions/{id}/edit', [UserController::class, 'viewEditTransactionPage']);
    Route::post('/transactions/{id}/edit', [UserController::class, 'updateTransaction']);
    
    Route::get('/transactions/{id}/delete', [UserController::class, 'deleteTransaction']);

    Route::get('/profile', [UserController::class, 'viewProfileSettings']);
    Route::post('/profile', [UserController::class, 'updateProfile']);

    Route::post('/transactions/export', [UserController::class, 'exportTransactions']);

    //Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/test', [UserController::class, 'test']);

});

require __DIR__.'/auth.php';
