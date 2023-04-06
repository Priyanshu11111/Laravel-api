<?php

use App\Http\Controllers\AssetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\SupplierController;

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
Route::apiResource('customers',CustomerController::class);
Route::post('login',[Authcontroller::class,'login']);
Route::get('notifications{id}',[NotificationController::class,'show']);
Route::middleware('auth:sanctum')->group(function (){
    Route::put('/profile',[CustomerController::class,'updateProfile']);
    Route::get('/profile',[CustomerController::class,'getAuthorizedUserInfo']);
    Route::post('/logout',[CustomerController::class,'logout']);
    Route::get('/activitylog',[CustomerController::class,'showactivity']);
    Route::get('/notifications',[CustomerController::class,'getNotifications']);
    Route::post('/markasread/{id}',[CustomerController::class,'markNotificationsAsRead']);
    Route::post('/refresh',[CustomerController::class,'refreshToken']);
    Route::apiResource('/types',TypesController::class);
    Route::apiResource('/supplier',SupplierController::class);
    Route::apiResource('/models',ModelsController::class);
});
Route::apiResource('asset',AssetController::class);
Route::post('customer',[CustomerController::class,'login']);
Route::post('customer/login',[CustomerController::class,'login']);


