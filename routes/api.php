<?php

use App\Http\Controllers\API\TestApiController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// first API route for testing

Route::get('/test', [TestApiController::class, 'test'])->name('test-api');