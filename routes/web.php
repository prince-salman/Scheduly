<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;

// ─────────────────────────────────────────
//  Guest-only auth routes
// ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// These two are accessible without being logged in (no redirect loop)
Route::get('/pending', [AuthController::class, 'pending'])->name('pending');
Route::get('/rejected', [AuthController::class, 'rejected'])->name('rejected');

// Logout (POST only, requires csrf)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─────────────────────────────────────────
//  Authenticated user routes
// ─────────────────────────────────────────
Route::group([], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Tasks / board
    Route::get('/tasks', [TaskController::class, 'board'])->name('tasks.board');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

// ─────────────────────────────────────────
//  Admin routes (auth + admin middleware)
// ─────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.detail');
    Route::patch('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [UserManagementController::class, 'reject'])->name('users.reject');
});
