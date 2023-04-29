<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

Route::post('/payments', [PaymentController::class, 'makePayment'])
    ->middleware('auth.api');

Route::post('/verify_expiration', [PaymentController::class, 'verifyExpiration'])
    ->middleware('auth.api');