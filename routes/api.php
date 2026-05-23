<?php

use Illuminate\Support\Facades\Route;

// Authenticated / API-key-protected routes
Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('valid.api.key')
    ->group(base_path('routes/api/v1.php'));
