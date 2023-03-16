<?php

use App\Http\Controllers\Contactcontroller;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Models\Customer; 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|pp
/* Route::get('/customer',[CustomerController::class,'index']);
Route::get('/customer/view',[CustomerController::class,'view']);
Route::post('/customer',[CustomerController::class,'store']);
Route::get('/customer/delete/{id}',[CustomerController::class,'delete']);
Route::get('/customer/edit/{id}',[CustomerController::class,'edit']);
Route::post('/customer/update/{id}',[CustomerController::class,'update']); */
Route::get('/upload',[ContactController::class,'index']);
Route::post('/upload',[ContactController::class,'upload']);



