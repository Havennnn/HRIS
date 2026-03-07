<?php

use App\Http\Controllers\Admin\DepartmentController;
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
