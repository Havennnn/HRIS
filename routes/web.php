<?php

use App\Http\Controllers\Admin\AttendanceLog\AttendanceLogController;
use App\Http\Controllers\Admin\Department\DepartmentController;
use App\Http\Controllers\Admin\Department\PositionController;
use App\Http\Controllers\Admin\Employee\AttendanceController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth:admin'])->group(function (): void {

    // Department Management Routes
    Route::prefix('departments')
    ->name('departments.')
    ->controller(DepartmentController::class)
    ->group(function (): void {
        Route::get('/', 'index')->middleware('can-list-departments')->name('index');
        Route::get('/create', 'create')->middleware('can-create-department')->name('create');
        Route::post('/', 'store')->middleware('can-create-department')->name('store');
        Route::get('/{department}/edit', 'edit')->middleware('can-update-department')->name('edit');
        Route::patch('/{department}', 'update')->middleware('can-update-department')->name('update');
        Route::delete('/{department}', 'destroy')->middleware('can-archive-department')->name('destroy');
        Route::patch('/{department}/restore', 'restore')->middleware('can-restore-department')->name('restore')->withTrashed();
    });

    // Position Management Routes
    Route::prefix('positions')
    ->name('positions.')
    ->controller(PositionController::class)
    ->group(function (): void {
        Route::get('/', 'index')->middleware('can-list-positions')->name('index');
        Route::get('/create', 'create')->middleware('can-create-position')->name('create');
        Route::post('/', 'store')->middleware('can-create-position')->name('store');
        Route::get('/{position}/edit', 'edit')->middleware('can-update-position')->name('edit');
        Route::patch('/{position}', 'update')->middleware('can-update-position')->name('update');
        Route::delete('/{position}', 'destroy')->middleware('can-archive-position')->name('destroy');
        Route::patch('/{position}/restore', 'restore')->middleware('can-restore-position')->name('restore')->withTrashed();
    });

    // Employee Management Routes
    Route::prefix('employees')
        ->name('employees.')
        ->controller(EmployeeController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-employees')->name('index');
            Route::get('/create', 'create')->middleware('can-create-employee')->name('create');
            Route::post('/', 'store')->middleware('can-create-employee')->name('store');
            Route::get('/{employee}/edit', 'edit')->middleware('can-update-employee')->name('edit');
            Route::patch('/{employee}', 'update')->middleware('can-update-employee')->name('update');
            Route::delete('/{employee}', 'destroy')->middleware('can-archive-employee')->name('destroy');
            Route::patch('/{employee}/restore', 'restore')->middleware('can-restore-employee')->name('restore')->withTrashed();

            // Attendance Routes
            Route::get('/{employee}/attendance', [AttendanceController::class, 'index'])->middleware('can-list-attendances')->name('attendance.index');
        });

    // Attendance Log Management Routes
    Route::prefix('attendance-logs')
        ->name('attendance-logs.')
        ->controller(AttendanceLogController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-attendance-logs')->name('index');
        });

    // Career Management Routes
    Route::prefix('careers')
        ->name('careers.')
        ->group(function (): void {
            Route::get('/', fn () => Inertia::render('Admin/Careers/Index'))->name('index');
            Route::get('/create', fn () => Inertia::render('Admin/Careers/Create'))->name('create');
            Route::post('/', fn () => redirect()->route('careers.index'))->name('store');
            Route::get('/{career}/edit', fn () => Inertia::render('Admin/Careers/Edit'))->name('edit');
            Route::patch('/{career}', fn () => redirect()->route('careers.index'))->name('update');
            Route::delete('/{career}', fn () => redirect()->route('careers.index'))->name('destroy');
            Route::patch('/{career}/restore', fn () => redirect()->route('careers.index'))->name('restore')->withTrashed();
        });

    // Application Management Routes
    Route::prefix('applications')
        ->name('applications.')
        ->group(function (): void {
            Route::get('/', fn () => Inertia::render('Admin/Applications/Index'))->name('index');
            Route::get('/create', fn () => Inertia::render('Admin/Applications/Create'))->name('create');
            Route::post('/', fn () => redirect()->route('applications.index'))->name('store');
            Route::get('/{application}/show', fn () => Inertia::render('Admin/Applications/Show'))->name('show');
            Route::patch('/{application}', fn () => redirect()->route('applications.index'))->name('update');
            Route::delete('/{application}', fn () => redirect()->route('applications.index'))->name('destroy');
            Route::patch('/{application}/restore', fn () => redirect()->route('applications.index'))->name('restore')->withTrashed();
        });
});
