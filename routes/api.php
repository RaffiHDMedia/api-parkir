<?php

use App\Http\Controllers\Api\ParkirController;
use App\Http\Controllers\paymentController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
   
   
});
Route::get('parkir-ticket',[ParkirController::class, 'index']);
Route::post('parkir-add',[ParkirController::class, 'store']);

Route::post('payment/ewallet',[PaymentController::class, 'create']);
Route::post('payment/cash',[PaymentController::class, 'paymentCash']);

Route::get('opengate',[ParkirController::class, 'opengate']);



