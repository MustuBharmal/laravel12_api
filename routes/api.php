<?php

use App\Http\Controllers\API\TestApiController;
use App\Http\Controllers\API\StudentApiController;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\BlogCategoryController;
use App\Http\Controllers\API\BlogPostController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// first API route for testing

Route::get('/test', [TestApiController::class, 'test'])->name('test-api');

Route::apiResource('/students', StudentApiController::class);

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Blog Category API routes
    Route::apiResource('/blog-categories', BlogCategoryController::class);

    // Blog Post API routes
    Route::apiResource('/blog-posts', BlogPostController::class);

    Route::post('/blog-posts-image/{post}', [BlogPostController::class, 'blogPostImage'])->name('blog-posts-image');
});

Route::get('/blog-categories', [BlogCategoryController::class, 'index']);
Route::get('/blog-posts', [BlogPostController::class, 'index']);
