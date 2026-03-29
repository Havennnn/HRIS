<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('valid.api.key')->group(base_path('routes/api/v1.php'));
