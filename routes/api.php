<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoItemController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([
    'middleware' => 'api'
], function ($router) {
    // User routes start
    Route::prefix('user')->group(function(){
        Route::post('/login',[AuthController::class, 'login']);
        Route::post('/register',[AuthController::class, 'register']);
        Route::post('/verify',[AuthController::class, 'verifyCode']);
        Route::get('/logout',[AuthController::class, 'logout']);
        // Todo list routes start
        Route::prefix('todo')->group(function(){
            Route::get("/search", [TodoItemController::class, 'search']);
            Route::get('/list', [TodoItemController::class, 'index']);
            Route::get('/view/{id}', [TodoItemController::class, 'show']);
            Route::post('/store',[TodoItemController::class, 'store']);
            Route::put('/{id}' , [TodoItemController::class, 'update']);
            Route::delete('/{id}', [TodoItemController::class, 'destroy'] );
        });
        // Todo list routes end
    });
    // User routes end
});
