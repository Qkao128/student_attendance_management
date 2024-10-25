<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassAdminController;
use App\Http\Controllers\CourseAdminController;
use App\Http\Controllers\UserAdminController;

Route::get('/', function () {
    return redirect()->route('login.index');
})->name('index');

Route::name('login.')->prefix('login')->group(function () {
    Route::get('/', [AuthController::class, 'loginIndex'])->name('index');
    Route::post('/', [AuthController::class, 'login'])->name('request');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard')->middleware('can:teacher');


Route::get('/dashboard/monitor', function () {
    return view('monitor.dashboard.index');
})->name('dashboard.monitor')->middleware('can:monitor');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// admin
Route::name('user.')->prefix('user')->group(function () {
    Route::get('select-search', [UserAdminController::class, 'selectOption'])->name('select_search');
});

Route::name('course.')->prefix('course')->group(function () {
    Route::get('/', [CourseAdminController::class, 'index'])->name('index');
    Route::post('/', [CourseAdminController::class, 'store'])->name('store');
    Route::patch('{id}', [CourseAdminController::class, 'update'])->name('update');
    Route::get('select-search', [CourseAdminController::class, 'selectOption'])->name('select_search');
    Route::get('{id}', [CourseAdminController::class, 'show'])->name('show');
    Route::delete('{id}', [CourseAdminController::class, 'destroy'])->name('destroy');
});

Route::name('class.')->prefix('class')->group(function () {
    Route::get('/', [ClassAdminController::class, 'index'])->name('index');
    Route::post('/', [ClassAdminController::class, 'store'])->name('store');
    Route::patch('{id}', [ClassAdminController::class, 'update'])->name('update');
    Route::get('{id}', [ClassAdminController::class, 'show'])->name('show');
    Route::delete('{id}', [ClassAdminController::class, 'destroy'])->name('destroy');
});
