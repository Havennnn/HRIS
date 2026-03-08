<?php

use App\Http\Controllers\Admin\Department\DepartmentController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Department\PositionController;
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

// Department Management Routes (requires admin auth)
Route::middleware(['auth:admin'])
    ->prefix('departments')
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

// Position Management Routes (requires admin auth)
Route::middleware(['auth:admin'])
    ->prefix('positions')
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

// Employee Management Routes (requires admin auth)
Route::middleware(['auth:admin'])
    ->prefix('employees')
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
    });
