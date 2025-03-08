<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Auth;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/', function (Request $request){
//     return Auth::user();
// });


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'LoggedInUser']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    Route::get('/my-posts', [PostController::class, 'myPosts']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('comments', CommentController::class);

    // For Comments
    Route::get('/posts/{id}/comments', [PostController::class, 'postWiseComments']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::get('/my-comments', [CommentController::class, 'myComments']);


    // For Post wise Tags
    Route::get('/posts/{id}/tags', [PostController::class, 'postWiseTags']);
    Route::post('/posts/{id}/tags', [PostController::class, 'attachTags']);

    // Admin only route for updating User Role
    Route::patch('/users/{id}/update-role', [UserController::class, 'updateRole']);
});
