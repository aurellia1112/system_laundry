<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/customer', [OrderController::class, 'index'])->name('customer.index');
Route::post('/customer/orders', [OrderController::class, 'store'])->name('customer.checkout');

Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/dashboard', [OrderController::class, 'adminDashboard'])
        ->middleware('verified')
        ->name('dashboard');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('admin.orders.updateStatus');
    Route::resource('services', ServicesController::class)->except('show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
