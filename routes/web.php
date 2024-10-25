<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseAdminController;

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


Route::name('course.')->prefix('course')->group(function () {
    Route::get('/', [CourseAdminController::class, 'index'])->name('index');
    Route::post('/', [CourseAdminController::class, 'store'])->name('store');
    Route::patch('{id}', [CourseAdminController::class, 'update'])->name('update');
    Route::get('{id}', [CourseAdminController::class, 'show'])->name('show');
    Route::delete('{id}', [CourseAdminController::class, 'destroy'])->name('destroy');
});
