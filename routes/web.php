<?php

use App\Http\Controllers\Admin\Application\ApplicationController;
use App\Http\Controllers\Admin\AttendanceLog\AttendanceLogController;
use App\Http\Controllers\Admin\Career\CareerController;
use App\Http\Controllers\Admin\Department\DepartmentController;
use App\Http\Controllers\Admin\Department\PositionController;
use App\Http\Controllers\Admin\Employee\AttendanceController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Holiday\HolidayController;
use App\Http\Controllers\Admin\Kpi\KpiController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Page\PageController;
use App\Http\Controllers\Admin\Payroll\PayrollController;
use App\Http\Controllers\Admin\PerformanceReview\PerformanceReviewController;
use App\Http\Controllers\Admin\Request\RequestController;
use App\Http\Controllers\Admin\Settings\PayoutConfigurationController;
use Illuminate\Support\Facades\Route;

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
            Route::get('/{employee}/edit', 'edit')->middleware('can-edit-employee')->name('edit');
            Route::patch('/{employee}', 'update')->middleware('can-update-employee')->name('update');
            Route::post('/{employee}/reset-password', 'resetPassword')->middleware('can-update-employee')->name('reset-password');
            Route::delete('/{employee}', 'destroy')->middleware('can-archive-employee')->name('destroy');
            Route::patch('/{employee}/restore', 'restore')->middleware('can-restore-employee')->name('restore')->withTrashed();

            // Import / Export
            Route::get('/export', 'export')->middleware('can-export-data')->name('export');
            Route::get('/manifest', 'manifest')->middleware('can-import-employees')->name('manifest');
            Route::post('/import', 'import')->middleware('can-import-employees')->name('import');

            // Employee Device Routes
            Route::prefix('/{employee}/device')
                ->name('device.')
                ->controller(EmployeeController::class)
                ->group(function (): void {
                    Route::patch('/', 'updateDevice')->middleware('can-update-employee')->name('update');
                });

            // Employee Contact Person Routes
            Route::prefix('/{employee}/contact-person')
                ->name('contact-person.')
                ->controller(EmployeeController::class)
                ->group(function (): void {
                    Route::patch('/', 'updateContactPerson')->middleware('can-update-employee')->name('update');
                });

            // Attendance Routes
            Route::prefix('/{employee}/attendance')
                ->name('attendance.')
                ->controller(AttendanceController::class)
                ->group(function (): void {
                    Route::get('/', 'index')->middleware('can-list-attendances')->name('index');
                });
        });

    // Attendance Log Management Routes
    Route::prefix('attendance-logs')
        ->name('attendance-logs.')
        ->controller(AttendanceLogController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-attendance-logs')->name('index');
            Route::get('/export', 'export')->middleware('can-export-data')->name('export');
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
            Route::post('/{career}/publish', 'publish')->middleware('can-update-career')->name('publish');
            Route::post('/{career}/draft', 'draft')->middleware('can-update-career')->name('draft');
            Route::delete('/{career}', 'destroy')->middleware('can-archive-career')->name('destroy');
            Route::patch('/{career}/restore', 'restore')->middleware('can-restore-career')->name('restore')->withTrashed();
        });

    // Application Management Routes
    Route::prefix('applications')
        ->name('applications.')
        ->controller(ApplicationController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-applications')->name('index');
            Route::get('/{application}', 'show')->middleware('can-view-application')->name('show');
            Route::post('/{application}/interview', 'interview')->middleware('can-interview-application')->name('interview');
            Route::post('/{application}/reject', 'reject')->middleware('can-reject-application')->name('reject');
            Route::post('/{application}/hire', 'hire')->middleware('can-hire-application')->name('hire');
            Route::delete('/{application}', 'destroy')->middleware('can-archive-application')->name('destroy');
            Route::patch('/{application}/restore', 'restore')->middleware('can-restore-application')->name('restore')->withTrashed();
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
            Route::get('/export', 'export')->middleware('can-export-data')->name('export');
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

    // Settings Routes
    Route::prefix('settings')
        ->name('settings.')
        ->group(function (): void {
            // KPI Management (selection for performance reviews)
            Route::prefix('kpis')
                ->name('kpis.')
                ->controller(KpiController::class)
                ->group(function (): void {
                    Route::get('/', 'index')->middleware('can-list-kpis')->name('index');
                    Route::get('/create', 'create')->middleware('can-create-kpi')->name('create');
                    Route::post('/', 'store')->middleware('can-create-kpi')->name('store');
                    Route::get('/{kpi}/edit', 'edit')->middleware('can-update-kpi')->name('edit');
                    Route::patch('/{kpi}', 'update')->middleware('can-update-kpi')->name('update');
                    Route::delete('/{kpi}', 'destroy')->middleware('can-archive-kpi')->name('destroy');
                    Route::patch('/{kpi}/restore', 'restore')->middleware('can-restore-kpi')->name('restore')->withTrashed();
                });

            Route::prefix('payout-configurations')
                ->name('payout-configurations.')
                ->controller(PayoutConfigurationController::class)
                ->group(function (): void {
                    Route::get('/', 'index')->middleware('can-list-payout-configurations')->name('index');
                    Route::patch('/{payoutConfiguration}', 'update')->middleware('can-update-payout-configuration')->name('update');
                    Route::post('/reset', 'reset')->middleware('can-update-payout-configuration')->name('reset');
                });
        });

    // Performance Review Management Routes
    Route::prefix('performance-reviews')
        ->name('performance-reviews.')
        ->controller(PerformanceReviewController::class)
        ->group(function (): void {
            Route::get('/', 'index')->middleware('can-list-performance-reviews')->name('index');
            Route::get('/create', 'create')->middleware('can-create-performance-review')->name('create');
            Route::post('/', 'store')->middleware('can-create-performance-review')->name('store');
            Route::get('/{performanceReview}/edit', 'edit')->middleware('can-update-performance-review')->name('edit');
            Route::patch('/{performanceReview}', 'update')->middleware('can-update-performance-review')->name('update');
            Route::delete('/{performanceReview}', 'destroy')->middleware('can-archive-performance-review')->name('destroy');
            Route::patch('/{performanceReview}/restore', 'restore')->middleware('can-restore-performance-review')->name('restore')->withTrashed();
        });

    // Notification Routes
    Route::prefix('notifications')
        ->name('notifications.')
        ->controller(NotificationController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/all/{notification?}', 'all')->name('all');
            Route::post('/{id}/read', 'read')->name('read');
            Route::post('/read-all', 'readAll')->name('read-all');
        });
});
