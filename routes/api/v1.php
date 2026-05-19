<?php

use App\Http\Controllers\Api\V1\Application\ApplicationController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Career\CareerController;
use App\Http\Controllers\Api\V1\Employee\EmployeeController;
use App\Http\Controllers\Api\V1\Payroll\PayrollController;
use App\Http\Controllers\Api\V1\PerformanceReview\PerformanceReviewController;
use App\Http\Controllers\Api\V1\Request\RequestController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::prefix('auth')
    ->name('auth.')
    ->controller(AuthController::class)
    ->group(function (): void {
        // Guest routes
        Route::post('/login', 'login')->name('login');
        Route::post('/password/forgot', 'forgotPassword')->name('password.forgot');
        Route::get('/password/reset/{token}', 'verifyResetToken')->name('password.reset');
        Route::post('/password/reset', 'updatePassword')->name('password.update');
        Route::post('/logout', 'logout')->middleware('auth:sanctum')->name('logout');
    });

Route::middleware('auth:sanctum')->group(function (): void {
    // Attendance Route
    Route::prefix('attendance')
        ->name('attendance.')
        ->controller(AttendanceController::class)
        ->group(function (): void {
            Route::get('/', 'list')->name('list');
            Route::get('/today', 'today')->name('today');
            Route::post('/time-in', 'timeIn')->name('time-in');
            Route::post('/time-out', 'timeOut')->name('time-out');
        });

    // Employee Route
    Route::prefix('employee')
        ->name('employee.')
        ->controller(EmployeeController::class)
        ->group(function (): void {
            Route::get('/', 'show')->name('show');
            Route::get('/documents', 'listDocuments')->name('documents.list');
            Route::post('/documents', 'uploadDocuments')->name('documents');
            Route::get('/performance-metrics', 'performanceMetrics')->name('performance-metrics');
        });

    // Payroll Routes
    Route::prefix('payrolls')
        ->name('payrolls.')
        ->controller(PayrollController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/latest', 'latest')->name('latest');
            Route::get('/{payroll}', 'show')->name('show');
        });

    // Request Routes
    Route::prefix('requests')
        ->name('requests.')
        ->controller(RequestController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/{request}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
        });

    // Performance Review Routes
    Route::prefix('performance-reviews')
        ->name('performance-reviews.')
        ->controller(PerformanceReviewController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/{performanceReview}', 'show')->name('show');
        });
});

// Career Routes (public)
Route::prefix('careers')
    ->name('careers.')
    ->controller(CareerController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/{career}', 'show')->name('show');
    });

// Application Routes (public)
Route::prefix('applications')
    ->name('applications.')
    ->controller(ApplicationController::class)
    ->group(function (): void {
        Route::post('/', 'store')->name('store');
    });
