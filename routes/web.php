<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Models\Service;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\CapsterController;
use App\Http\Controllers\CapsterProfileController;

Route::get('/', function () {
    $services = Service::where('is_active', true)
        ->where('is_public', true)
        ->orderBy('category')
        ->orderBy('sort_order')
        ->get(['code','name','price','category']);

    return view('tilmidhome', compact('services'));
});

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/booking', fn() => view('booking'));
Route::get('/booking', [BookingController::class, 'create']);
Route::get('/booking/success/{code}', [BookingController::class, 'success']);
Route::post('/booking', [BookingController::class, 'store']);

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
    Route::get('/admin/price', [AdminServiceController::class, 'index']);
    Route::post('/admin/price', [AdminServiceController::class, 'store']);
    Route::put('/admin/price/{service}', [AdminServiceController::class, 'update']);
    Route::delete('/admin/price/{service}', [AdminServiceController::class, 'destroy']);
    Route::post('/admin/price/{service}/toggle', [AdminServiceController::class, 'toggleActive']); // optional

    // Capster
    Route::post('/admin/capsters', [AdminController::class, 'storeCapster']);
    Route::post('/admin/capsters/{user}/toggle', [AdminController::class, 'toggleCapster']);
    Route::delete('/admin/capsters/{user}', [AdminController::class, 'destroyCapster']);

    // Finance
    Route::post('/admin/finance', [AdminController::class, 'storeFinance']);
    Route::put('/admin/finance/{tx}', [AdminController::class, 'updateFinance']);
    Route::delete('/admin/finance/{tx}', [AdminController::class, 'destroyFinance']);
    Route::get('/admin/finance/export', [AdminController::class, 'exportLedger']);
});

Route::middleware(['auth','role:capster'])->group(function () {
    Route::get('/capster', [CapsterController::class, 'dashboard']);
    Route::get('/capster/history/export', [CapsterController::class, 'exportHistory']);
    Route::post('/capster/booking/{booking}/done', [CapsterController::class, 'markDone']);
    Route::post('/capster/booking/{booking}/cancel', [CapsterController::class, 'cancel']);
    Route::post('/capster/addon', [CapsterController::class, 'storeAddon']);
    Route::post('/capster/walkin', [CapsterController::class, 'storeWalkin']);
    Route::get('/capster/profile', [CapsterProfileController::class, 'edit']);
    Route::post('/capster/profile', [CapsterProfileController::class, 'updateProfile']);
    Route::post('/capster/profile/password', [CapsterProfileController::class, 'updatePassword']);
    Route::get('/capster/notify', [\App\Http\Controllers\CapsterController::class, 'notify'])
        ->middleware(['auth','role:capster']);
});