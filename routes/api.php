<?php

use App\Http\Controllers\AssetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Http\Controllers\CustomerController;
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
Route::apiResource('customers',CustomerController::class);
Route::post('login',[Authcontroller::class,'login']);
Route::middleware('auth:sanctum')->group(function (){
    Route::get('user',[Authcontroller::class,'user']);
});
Route::apiResource('asset',AssetController::class);

Route::post('customer',[CustomerController::class,'login']);
Route::post('customer/login',[CustomerController::class,'login']);
