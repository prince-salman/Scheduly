<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;

//guest

Route::middleware('guest')->group(function () {

    Route::get('/register', [UserController::class, 'showRegister'])->name('register');
   
Route::post('/register', [UserController::class, 'store'])
    ->name('register.store');
    

    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
});

//auth & fitur

Route::middleware('auth')->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/pending', [UserController::class, 'pending'])->name('pending');
    Route::get('/rejected', [UserController::class, 'rejected'])->name('rejected');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

     Route::get('/tasks', [TaskController::class, 'board'])
        ->name('tasks.board');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');

    Route::patch('/tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy');

    Route::patch('/tasks/{task}/move', [TaskController::class, 'moveStatus'])
        ->name('tasks.move');

    Route::post('/tasks/{task}/timer/start', [TaskController::class, 'startTimer'])
        ->name('tasks.timer.start');

    Route::post('/tasks/{task}/timer/stop', [TaskController::class, 'stopTimer'])
        ->name('tasks.timer.stop');

    Route::patch('/tasks/{task}/subtasks/{index}', [TaskController::class, 'toggleSubtask'])
        ->name('tasks.subtask.toggle');

    Route::get('/tasks-trash', [TaskController::class, 'trash'])
        ->name('tasks.trash');
    Route::get('/tasks/export/pdf', [TaskController::class, 'exportPdf'])
    ->name('tasks.export.pdf');
    Route::get('/tasks/export/csv', [TaskController::class, 'exportCsv'])
    ->name('tasks.export.csv');
    Route::patch('/tasks/{id}/restore', [TaskController::class, 'restore'])
        ->name('tasks.restore');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.events.store');
    Route::delete('/calendar/events/{event}', [CalendarController::class, 'destroy'])->name('calendar.events.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read.all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread.count');
});

//admin

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'adminStore'])
            ->name('users.store');

        Route::get('/users/{id}', [UserController::class, 'detail'])
            ->name('users.detail');

        Route::patch('/users/{id}/approve', [UserController::class, 'approve'])
            ->name('users.approve');

        Route::patch('/users/{id}/reject', [UserController::class, 'reject'])
            ->name('users.reject');

        Route::patch('/users/{id}/role', [UserController::class, 'updateRole'])
            ->name('users.role');

        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle');
    });