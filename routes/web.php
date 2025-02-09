<?php

use App\Enums\UserType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\ClassAdminController;
use App\Http\Controllers\CourseAdminController;
use App\Http\Controllers\HolidayAdminController;
use App\Http\Controllers\MonitorAdminController;
use App\Http\Controllers\StudentAdminController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\AttendanceAdminController;
use App\Http\Controllers\AttendanceStatisticsAdminController;


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


Route::prefix('admin')->middleware(['auth', 'check_role:' . UserType::SuperAdmin()->key . '|' . UserType::Admin()->key . '|' . UserType::Monitor()->key])->group(function () {
    // 管理員路由
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/data', [DashboardAdminController::class, 'data'])->name('dashboard.data');
    Route::post('/dashboard/pieChartData', [DashboardAdminController::class, 'pieChartData'])->name('dashboard.pieChartData');
    Route::post('/dashboard/isHoliday', [DashboardAdminController::class, 'getHolidayStatus'])->name('dashboard.isHoliday');



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
        Route::post('{teacherId}/monitor', [UserAdminController::class, 'storeMonitor'])->name('monitor.store');
        Route::get('{teacherId}/monitor/{id}/edit', [UserAdminController::class, 'editMonitor'])->name('monitor.edit');
        Route::patch('{teacherId}/change-password/{id}', [UserAdminController::class, 'updateMonitorPassword'])->name('password.monitor.update');
        Route::patch('{teacherId}/monitor/{id}', [UserAdminController::class, 'updateMonitor'])->name('monitor.update');
        Route::get('{teacherId}/monitor/{id}', [UserAdminController::class, 'showMonitor'])->name('monitor.show');
        Route::delete('{teacherId}/monitor/{id}', [UserAdminController::class, 'destroyMonitor'])->name('monitor.destroy');
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
        Route::get('select-search', [ClassAdminController::class, 'selectOption'])->name('select_search');
        Route::get('{id}', [ClassAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [ClassAdminController::class, 'destroy'])->name('destroy');
    });

    // 學生管理
    Route::name('student.')->prefix('student')->group(function () {
        Route::post('{classId}', [StudentAdminController::class, 'store'])->name('store');
        Route::get('{classId}/{id}/edit', [StudentAdminController::class, 'edit'])->name('edit');
        Route::patch('{classId}/{id}', [StudentAdminController::class, 'update'])->name('update');
        Route::get('select-search', [StudentAdminController::class, 'selectOption'])->name('select_search');
        Route::delete('{classId}/{id}', [StudentAdminController::class, 'destroy'])->name('destroy');
    });

    Route::post('datatable', [StudentAdminController::class, 'datatable'])->name('datatable');

    // 出席管理
    Route::name('attendance.')->prefix('attendance')->group(function () {
        Route::get('/', [AttendanceAdminController::class, 'index'])->name('index');
        Route::post('{classId}/{date?}', [AttendanceAdminController::class, 'store'])->name('store');
        Route::get('{id}/{date?}', [AttendanceAdminController::class, 'show'])->name('show');
        Route::delete('{id}', [AttendanceAdminController::class, 'destroy'])->name('destroy');
    });

    // 出席數據
    Route::name('attendance_statistics.')->prefix('attendance-statistics')->group(function () {
        Route::get('/', [AttendanceStatisticsAdminController::class, 'index'])->name('index');
        Route::get('{id}/{date?}', [AttendanceStatisticsAdminController::class, 'show'])->name('show');
        Route::post('/attendance-statistics/pieChartData', [AttendanceStatisticsAdminController::class, 'pieMonthlyChartData'])->name('pieMonthlyChartData');
    });


    // 假期管理
    Route::name('holiday.')->prefix('holiday')->group(function () {
        Route::get('/', [HolidayAdminController::class, 'index'])->name('index');
        Route::post('/', [HolidayAdminController::class, 'store'])->name('store');
        Route::patch('{id}', [HolidayAdminController::class, 'update'])->name('update');
        Route::post('holidays', [HolidayAdminController::class, 'getHolidays'])->name('getHolidays');
        Route::delete('{id}', [HolidayAdminController::class, 'destroy'])->name('destroy');
    });
});
