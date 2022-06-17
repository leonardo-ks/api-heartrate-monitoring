<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//API route for register new user
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

//Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function(Request $request) {
        return ['success' => true, 'message'=> 'Profile retrieved','data' => auth()->user(),];
    });
    Route::resource('data', App\Http\Controllers\API\DataController::class);
    Route::get('/average', [App\Http\Controllers\API\DataController::class, 'average']);
    Route::put('/update-user', [App\Http\Controllers\API\AuthController::class, 'update']);
    Route::post('/change-password', [App\Http\Controllers\API\AuthController::class, 'updatePassword']);
    Route::post('/find-data', [App\Http\Controllers\API\DataController::class, 'findData']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    Route::get('/id', function(Request $request) {
        return ['success' => true, 'message'=> 'Id retrieved', 'id' => auth()->user()->id,];
    });
});
