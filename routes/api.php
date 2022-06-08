<?php

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


Route::post('/register',[\App\Http\Controllers\AuthController::class,'register'])->name('api.register');
Route::post('/login',[\App\Http\Controllers\AuthController::class,'login'])->name('api.login');
Route::post('/forgot_password', [\App\Http\Controllers\AuthController::class,'forgotPassword'])->name('api.forgotPassword');
Route::post('/verify_otp', [\App\Http\Controllers\AuthController::class,'verifyOtp'])->name('api.verifyOtp');
Route::post('/resend_otp', [\App\Http\Controllers\AuthController::class,'resendOtp'])->name('api.resendOtp');
Route::post('/reset_password', [\App\Http\Controllers\AuthController::class,'resetPassword'])->name('api.resetPassword');


Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::get('/me',function(Request $request){
       return auth()->user();
    });
    Route::post('/logout',[\App\Http\Controllers\AuthController::class,'logout'])->name('api.logout');
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
