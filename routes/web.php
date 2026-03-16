<?php

use App\Http\Controllers\Admin\Career\CareerController;
use App\Http\Controllers\Admin\AttendanceLog\AttendanceLogController;
use App\Http\Controllers\Admin\Calendar\CalendarController;
use App\Http\Controllers\Admin\Department\DepartmentController;
use App\Http\Controllers\Admin\Department\PositionController;
use App\Http\Controllers\Admin\Employee\AttendanceController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Holiday\HolidayController;
use App\Http\Controllers\Admin\Payroll\PayrollController;
use App\Http\Controllers\Admin\Request\RequestController;
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
            Route::get('/{employee}/attendance', [AttendanceController::class, 'index'])
                ->whereNumber('employee')
                ->middleware('can-list-attendances')
                ->name('attendance.index');
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
        ->controller(CareerController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-careers')->name('index');
            Route::get('/create', 'create')->middleware('can-create-career')->name('create');
            Route::post('/', 'store')->middleware('can-create-career')->name('store');
            Route::get('/{career}/edit', 'edit')->middleware('can-update-career')->name('edit');
            Route::patch('/{career}', 'update')->middleware('can-update-career')->name('update');
            Route::delete('/{career}', 'destroy')->middleware('can-archive-career')->name('destroy');
            Route::patch('/{career}/restore', 'restore')->middleware('can-restore-career')->name('restore')->withTrashed();
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

    // Request Management Routes
    Route::prefix('requests')
        ->name('requests.')
        ->controller(RequestController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-requests')->name('index');
            Route::get('/{request}', 'show')->middleware('can-view-request')->name('show');
            Route::post('/{request}/approve', 'approve')->middleware('can-approve-request')->name('approve');
            Route::post('/{request}/reject', 'reject')->middleware('can-reject-request')->name('reject');
            Route::post('/{request}/cancel', 'cancel')->middleware('can-cancel-request')->name('cancel');
            Route::post('/{request}/complete', 'complete')->middleware('can-complete-request')->name('complete');
            Route::delete('/{request}', 'destroy')->middleware('can-archive-request')->name('destroy');
            Route::patch('/{request}/restore', 'restore')->middleware('can-restore-request')->name('restore')->withTrashed();
        });

    // Payroll Management Routes
    Route::prefix('payrolls')
        ->name('payrolls.')
        ->controller(PayrollController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-payrolls')->name('index');
            Route::get('/{payroll}', 'show')->middleware('can-list-payrolls')->name('show');
        });

    // Holiday Management Routes
    Route::prefix('holidays')
        ->name('holidays.')
        ->controller(HolidayController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-holidays')->name('index');
            Route::get('/create', 'create')->middleware('can-create-holiday')->name('create');
            Route::post('/', 'store')->middleware('can-create-holiday')->name('store');
            Route::get('/{holiday}/edit', 'edit')->middleware('can-update-holiday')->name('edit');
            Route::patch('/{holiday}', 'update')->middleware('can-update-holiday')->name('update');
            Route::delete('/{holiday}', 'destroy')->middleware('can-archive-holiday')->name('destroy');
            Route::patch('/{holiday}/restore', 'restore')->middleware('can-restore-holiday')->name('restore')->withTrashed();
        });
});
