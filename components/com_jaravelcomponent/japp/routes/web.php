<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo "<h1>Laravel is running!</h1>";
    echo "<p>Version: " . app()->version() . "</p>";
    echo "<p>Path: " . base_path() . "</p>";
    echo "<p>Using namespace: JaravelComponent</p>";
});

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Jaravel test route working!',
        'laravel_version' => app()->version(),
        'namespace' => 'JaravelComponent'
    ]);
});