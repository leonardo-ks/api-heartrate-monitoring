<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DataController;
use App\Http\Controllers\API\NotificationController;
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
    Route::resource('data', DataController::class);
    Route::get('/average', [DataController::class, 'average']);
    Route::get('/data/{start}/{end}', [DataController::class, 'byDate']);
    Route::put('/update-user', [AuthController::class, 'update']);
    Route::post('/add-contact', [AuthController::class, 'addContact']);
    Route::post('/change-password', [AuthController::class, 'updatePassword']);
    Route::post('/find-data', [DataController::class, 'findData']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/notification', [NotificationController::class, 'send']);
});
