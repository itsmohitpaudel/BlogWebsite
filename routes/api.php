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
    Route::apiResource('users', UserController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('comments', CommentController::class);

    // For Comments
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // For Tags
    Route::post('/posts/{id}/tags', [TagController::class, 'attachTags']);

    // Admin only route for updating User Role
    Route::patch('/users/{id}/update-role', [UserController::class, 'updateRole']);
});
