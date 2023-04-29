<?php

use App\Http\Controllers\AssetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RequestController;
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
Route::get('notifications{id}',[NotificationController::class,'show']);
Route::middleware('auth:sanctum')->group(function (){
    Route::apiResource('users',CustomerController::class);
    Route::put('/profile',[CustomerController::class,'updateProfile']);
    Route::get('/profile',[CustomerController::class,'getAuthorizedUserInfo']);
    Route::post('/logout',[CustomerController::class,'logout']);
    Route::get('/activitylog',[CustomerController::class,'showactivity']);
    Route::get('/notifications',[CustomerController::class,'getNotifications']);
    Route::get('/allnotifications',[CustomerController::class,'getallnotifications']);
    Route::post('/refresh', [CustomerController::class, 'refreshToken']);
    Route::apiResource('/types',TypesController::class);
    Route::apiResource('/supplier',SupplierController::class);
    Route::apiResource('/models',ModelsController::class);
    Route::apiResource('/request',RequestController::class);
    Route::post('/markasread/{id}',[CustomerController::class,'markNotificationsAsRead']);
    Route::get('/get',[RequestController::class,'index']);
    Route::put('/action/{id}',[RequestController::class,'update']);
    Route::get('/requestlist',[RequestController::class,'requestData']);
    Route::get('/gettype/{id}',[RequestController::class,'getTypeName']);
    Route::get('/getmodel/{id}',[ModelsController::class,'getModelName']);
    Route::get('/name/{id}',[RequestController::class,'getModelName']);
    Route::get('/getall',[ModelsController::class,'getAllModels']);
    Route::get('/getrequest/{id}',[RequestController::class,'getneed']);
    Route::get('/getallrequest',[RequestController::class,'getallrequest']);
    Route::get('/viewmodel/{id}',[ModelsController::class,'viewmodels']);
    Route::get('/userrequest',[RequestController::class,'getauthrequest']);
    Route::post('/readall',[CustomerController::class,'markAsReadall']);
    Route::post('/rolecreate',[CustomerController::class,'role']);
    Route::get('/getrole',[CustomerController::class,'getUserRole']);
    Route::post('/createpermission',[PermissionsController::class,'store']);
    Route::get('/modules',[ModulesController::class,'index']);
});
Route::apiResource('asset',AssetController::class);
Route::post('customer',[CustomerController::class,'login']); 
Route::post('users/login',[CustomerController::class,'login']);


