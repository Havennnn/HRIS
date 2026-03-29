<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'HRIS API v1 is reachable.',
        'version' => 'v1',
    ]);
});
