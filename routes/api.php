<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

Route::post('/orders', [OrderController::class, 'create']);
Route::post('/orders/createtracking', [OrderController::class, 'createtracking']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/getbyorderid', [OrderController::class, 'getbyorderid']);
Route::get('/orders/gettracking', [OrderController::class, 'gettracking']);