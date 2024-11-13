<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassAdminController;
use App\Http\Controllers\CourseAdminController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\StudentAdminController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('index');

Route::name('login')->prefix('login')->group(function () {
    Route::get('/', [AuthController::class, 'loginIndex']);
    Route::post('/', [AuthController::class, 'login'])->name('.request');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard')->middleware('can:admin');

    Route::get('/dashboard/monitor', function () {
        return view('monitor.dashboard.index');
    })->name('dashboard.monitor')->middleware('can:monitor');

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin routes
    Route::name('user.')->prefix('user')->group(function () {
        Route::get('select-search', [UserAdminController::class, 'selectOption'])->name('select_search');
    });

    Route::name('course.')->prefix('course')->group(function () {
        Route::get('/', [CourseAdminController::class, 'index'])->name('index');
        Route::post('/', [CourseAdminController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CourseAdminController::class, 'edit'])->name('edit');
        Route::patch('{id}', [CourseAdminController::class, 'update'])->name('update');
        Route::get('select-search', [CourseAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [CourseAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [CourseAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name('class.')->prefix('class')->group(function () {
        Route::get('/', [ClassAdminController::class, 'index'])->name('index');
        Route::post('/', [ClassAdminController::class, 'store'])->name('store');
        Route::get('{id}/edit', [ClassAdminController::class, 'edit'])->name('edit');
        Route::patch('{id}', [ClassAdminController::class, 'update'])->name('update');
        Route::get('{id}', [ClassAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [ClassAdminController::class, 'destroy'])->name('destroy');
    });

    Route::name('student.')->prefix('student')->group(function () {
        Route::post('{classId}/', [StudentAdminController::class, 'store'])->name('store');
        Route::get('{classId}/id}', [StudentAdminController::class, 'edit'])->name('edit');
        Route::patch('{classId}/{id}', [StudentAdminController::class, 'update'])->name('update');
        Route::delete('{classId}/{id}', [StudentAdminController::class, 'destroy'])->name('destroy');
    });

    Route::post('datatable', [StudentAdminController::class, 'datatable'])->name('datatable');
});
