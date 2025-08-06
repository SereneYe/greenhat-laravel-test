<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Greenhat Laravel Assessment Application',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});
