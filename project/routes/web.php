<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return response()->json([
        'message' => 'Leave Management System API',
        'version' => '1.0.0',
        'documentation' => '/api/docs',
        'status' => 'active'
    ]);
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found. Please check the API documentation.'
    ], 404);
});