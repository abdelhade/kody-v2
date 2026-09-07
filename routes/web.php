<?php

use App\Http\Controllers\Api\SystemStatusController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\ReportController;

// Public routes
Route::get('/', function () {
    return \Inertia\Inertia::render('Welcome');
})->name('welcome');

// Auth routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Chart of Accounts, Clients, Suppliers, Funds
    Route::resource('accounts', AccountController::class)->only(['index', 'store']);

    // Items & Inventory Management
    Route::resource('items', ItemController::class)->only(['index', 'store', 'destroy']);

    // POS (Point of Sale)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Shifts
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts/close', [ShiftController::class, 'close'])->name('shifts.close');

    // HR & Payroll
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('/employees', [HrController::class, 'employees'])->name('employees');
        Route::get('/attendance', [HrController::class, 'attendance'])->name('attendance');
        Route::get('/payroll', [HrController::class, 'payroll'])->name('payroll');
        Route::post('/payroll/process', [HrController::class, 'processPayroll'])->name('payroll.process');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/account-statement', [ReportController::class, 'accountStatement'])->name('statement');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial_balance');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit_loss');
    });
});

// API Routes
Route::prefix('api/v1')->group(function () {
    Route::get('/status', [SystemStatusController::class, 'status']);
    Route::get('/items', [SystemStatusController::class, 'items']);
    Route::get('/accounts', [SystemStatusController::class, 'accounts']);
});
