<?php

use App\Enums\UserType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\ClassAdminController;
use App\Http\Controllers\CourseAdminController;
use App\Http\Controllers\StudentAdminController;


Route::get('/', function () {
    return redirect()->route('login.index');
})->name('index');

Route::group(['middleware' => 'guest'], function () {
    Route::name('login.')->prefix('login')->group(function () {
        Route::get('/', [AuthController::class, 'loginIndex'])->name('index');
        Route::post('/', [AuthController::class, 'login'])->name('request');
    });
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware(['auth', 'check_role:' . UserType::SuperAdmin()->key . '|' . UserType::Admin()->key])->group(function () {
    // 管理員路由
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // 使用者管理
    Route::name('user.')->prefix('account')->group(function () {
        Route::get('/', [UserAdminController::class, 'index'])->name('index');
        Route::post('/', [UserAdminController::class, 'store'])->name('store');
        Route::get('{id}/edit', [UserAdminController::class, 'edit'])->name('edit');
        Route::patch('change-password/{id}', [UserAdminController::class, 'updatePassword'])->name('password.update');
        Route::patch('{id}', [UserAdminController::class, 'update'])->name('update');
        Route::get('select-search', [UserAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [UserAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [UserAdminController::class, 'destroy'])->name('destroy');
    });

    // 課程管理
    Route::name('course.')->prefix('course')->group(function () {
        Route::get('/', [CourseAdminController::class, 'index'])->name('index');
        Route::post('/', [CourseAdminController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CourseAdminController::class, 'edit'])->name('edit');
        Route::patch('{id}', [CourseAdminController::class, 'update'])->name('update');
        Route::get('select-search', [CourseAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [CourseAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [CourseAdminController::class, 'destroy'])->name('destroy');
    });

    // 班級管理
    Route::name('class.')->prefix('class')->group(function () {
        Route::get('/', [ClassAdminController::class, 'index'])->name('index');
        Route::post('/', [ClassAdminController::class, 'store'])->name('store');
        Route::get('{id}/edit', [ClassAdminController::class, 'edit'])->name('edit');
        Route::patch('{id}', [ClassAdminController::class, 'update'])->name('update');
        Route::get('{id}', [ClassAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [ClassAdminController::class, 'destroy'])->name('destroy');
    });

    // 學生管理
    Route::name('student.')->prefix('student')->group(function () {
        Route::post('{classId}/', [StudentAdminController::class, 'store'])->name('store');
        Route::get('{classId}/{id}/edit', [StudentAdminController::class, 'edit'])->name('edit');
        Route::patch('{classId}/{id}', [StudentAdminController::class, 'update'])->name('update');
        Route::delete('{classId}/{id}', [StudentAdminController::class, 'destroy'])->name('destroy');
    });

    Route::post('datatable', [StudentAdminController::class, 'datatable'])->name('datatable');
    //
});




Route::name('monitor.')->prefix('monitor')->middleware(['auth', 'check_role:' . UserType::Monitor()])->group(function () {
    Route::get('/dashboard', function () {
        return view('monitor.dashboard.index');
    })->name('dashboard');
});
