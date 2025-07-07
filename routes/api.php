<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/smsRequest', [App\Http\Controllers\TransactionController::class, 'smsRequest']);
Route::post('/smsRequest2', [App\Http\Controllers\TransactionController::class, 'smsRequest2']);

Route::post('/smsSCB', [App\Http\Controllers\TransactionController::class, 'sms_scb']);
Route::post('/smsOTP', [App\Http\Controllers\TransactionController::class, 'smsOTP']);

Route::get('/Checktransfer', [App\Http\Controllers\TransactionController::class, 'Checktransfer']);
Route::post('/upDaterefNo', [App\Http\Controllers\TransactionController::class, 'upDaterefNo']);
Route::post('/updateOTP', [App\Http\Controllers\TransactionController::class, 'updateOTP']);
Route::post('/approvewithdraw', [App\Http\Controllers\TransactionController::class, 'approvewithdraw']);
Route::get('/getOTP/{id}', [App\Http\Controllers\TransactionController::class, 'getOTP']);
Route::get('/getTranfer/{id}', [App\Http\Controllers\TransactionController::class, 'getTranfer']);


Route::post('/jili', [App\Http\Controllers\CallbackController::class, 'jili']);
Route::post('/jdb', [App\Http\Controllers\CallbackController::class, 'jdb']);
Route::post('/tf', [App\Http\Controllers\CallbackController::class, 'tf']);
Route::post('/sbo', [App\Http\Controllers\CallbackController::class, 'sbo']);

Route::post('/transfer_to_Bank', [App\Http\Controllers\TMN_Controller::class, 'transfer_to_Bank']);
Route::post('/transfer_to_Mobile', [App\Http\Controllers\TMN_Controller::class, 'transfer_to_Mobile']);
Route::get('/fetchTransactionHistory', [App\Http\Controllers\TMN_Controller::class, 'fetchTransactionHistory']);
Route::get('/fetchTransactionInfo', [App\Http\Controllers\TMN_Controller::class, 'fetchTransactionInfo']);
Route::get('/lastTransactionHistory', [App\Http\Controllers\TMN_Controller::class, 'lastTransactionHistory']);


Route::post('/smsTest', [App\Http\Controllers\TransactionController::class, 'smsTest']);

// Route::post('/trueCallback', [App\Http\Controllers\TransactionController::class, 'trueCallback']);

Route::get('/checkdepositTMN/{id}', [App\Http\Controllers\TransactionController::class, 'checkdepositTMN']);
Route::get('/checkdeposit/{id}', [App\Http\Controllers\TransactionController::class, 'checkdeposit']);

Route::post('/check_token', [App\Http\Controllers\ManageMemberController::class, 'check_token']);
Route::post('/user-login', [App\Http\Controllers\SMSController::class, 'userLogin']);

//PaymenthubController
Route::post('/Payment', [App\Http\Controllers\PaymenthubController::class, 'processPayment']);
