<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\BlogPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/blog', BlogPostController::class)->except(['index', 'show'])->parameters([
        'blog' => 'blogPost',
    ]);
    Route::post('/blog/{blogPost}/restore', [BlogPostController::class, 'restore'])->withTrashed()->name('blog.restore');
    Route::delete('/blog/{blogPost}/force-delete', [BlogPostController::class, 'forceDelete'])->withTrashed()->name('blog.force-delete');
});

Route::controller(BlogPostController::class)->group(function () {
    Route::get('/blog/{blogPost}', 'show')->name('blog.show');
    Route::get('/blog', 'index')->name('blog.index');
});

Route::post('/authenticate', [ApiAuthController::class, 'authenticate'])->name('api.authenticate');
