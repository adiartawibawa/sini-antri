<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------
// SISI PENGUNJUNG
// -------------------------------------------------------
Route::get('/', fn () => redirect()->route('visitor.register', 'umum'));

// Form ambil antrian (diakses dari QR Code)
Route::get('/visitor/{locationCode?}', [VisitorController::class, 'register'])
    ->name('visitor.register');

Route::post('/visitor/queue/take', [VisitorController::class, 'takeQueue'])
    ->name('visitor.take');

// Tiket digital pengunjung
Route::get('/ticket/{uuid}', [VisitorController::class, 'ticket'])
    ->name('visitor.ticket');

// API: posisi terkini (dipanggil oleh JS)
Route::get('/ticket/{uuid}/position', [VisitorController::class, 'ticketPosition'])
    ->name('visitor.ticket.position');

// -------------------------------------------------------
// SISI OPERATOR / AUTH
// -------------------------------------------------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest:operator');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('operator')->name('operator.')->middleware('auth:operator')->group(function () {
    Route::get('/', [OperatorController::class, 'index'])->name('dashboard');

    // Aksi antrian (AJAX/JSON)
    Route::post('/queue/call', [OperatorController::class, 'call'])->name('queue.call');
    Route::post('/queue/{antrian}/recall', [OperatorController::class, 'recall'])->name('queue.recall');
    Route::post('/queue/{antrian}/skip', [OperatorController::class, 'skip'])->name('queue.skip');
    Route::post('/queue/{antrian}/complete', [OperatorController::class, 'complete'])->name('queue.complete');

    // API polling (fallback jika WebSocket tidak tersedia)
    Route::get('/queue/waiting', [OperatorController::class, 'waitingList'])->name('queue.waiting');
});

// -------------------------------------------------------
// SISI ADMINISTRATOR (is_operator = false)
// -------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware('auth:operator')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/reset', [AdminController::class, 'resetCounter'])->name('settings.reset');

    Route::get('/operators', [AdminController::class, 'operators'])->name('operators');
    Route::post('/operators', [AdminController::class, 'storeOperator'])->name('operators.store');
    Route::put('/operators/{user}', [AdminController::class, 'updateOperator'])->name('operators.update');
    Route::delete('/operators/{user}', [AdminController::class, 'deleteOperator'])->name('operators.delete');
});

// -------------------------------------------------------
// SISI DISPLAY (Layar TV)
// -------------------------------------------------------
Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/status', [DisplayController::class, 'status'])->name('display.status');

// QR Code Generator
Route::get('/admin/qrcode/{locationCode?}', [DisplayController::class, 'qrcode'])
    ->name('admin.qrcode');
