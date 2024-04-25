<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateCashier;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('/', [AuthController::class, 'authLogin'])->name('auth_login');
Route::post('/login', [AuthController::class, 'authLogin'])->name('auth_login');
Route::get('/add', [AuthController::class, 'addCashier'])->name('auth_register');
// Route::get('/dashboard', [POSController::class, 'dashboard'])->middleware(AuthenticateCashier::class)->name('dashboard');
Route::get('/dashboard', [POSController::class, 'dashboard'])->name('dashboard');
Route::get('/ticket_details', [POSController::class, 'ticketDetails'])->name('ticket_details');
Route::get('/history', [POSController::class, 'history'])->name('history');
Route::post('/sale', [POSController::class, 'sale'])->name('sale');
Route::get('/purchased_date', [POSController::class, 'purchasedDate'])->name('purchased_date');
Route::get('/cashier', [POSController::class, 'cashier'])->name('cashier');
Route::post('/cashier', [POSController::class, 'startShift'])->name('start_shift');