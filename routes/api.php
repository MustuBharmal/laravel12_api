<?php

use App\Http\Controllers\API\TestApiController;
use App\Http\Controllers\API\StudentApiController;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\BlogCategoryController;
use App\Http\Controllers\API\BlogPostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
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
    Route::apiResource('/blog-categories', BlogCategoryController::class)->middleware(['role:admin']);

    // Blog Post API routes
    Route::apiResource('/blog-posts', BlogPostController::class)->middleware(['role:admin,author']);

    Route::post('/blog-posts-image/{post}', [BlogPostController::class, 'blogPostImage'])->name('blog-posts-image')->middleware(['role:admin,author']);

    // Like API routes
    Route::post('/post/react', [LikeController::class, 'react'])->name('react');

    // Comment API routes
    Route::apiResource('comments', CommentController::class);

    Route::get('comments', [CommentController::class, 'index'])->name('index')->middleware(['role:admin']);

    Route::post('/comments/changestatus', [CommentController::class, 'changeStatus'])->name('changeStatus')->middleware(['role:admin']);
});

Route::get('/blog-categories', [BlogCategoryController::class, 'index']);
Route::get('/blog-posts', [BlogPostController::class, 'index']);
Route::get('/post/reactions/{post}', [LikeController::class, 'reactions'])->name('reactions');
Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('show');
