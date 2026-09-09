<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia\Inertia::render('Welcome');
})->name('welcome');

// Removed guest:landlord middleware to avoid redirect loops. Handled in Controller.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Workspace Provisioning (Must be logged into Landlord)
Route::middleware('auth:landlord')->group(function () {
    Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tenant Application (Must be logged into Web guard & Tenant middleware)
Route::middleware(['tenant', 'auth:web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
