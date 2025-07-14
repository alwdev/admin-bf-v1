<?php

use App\Http\Controllers\BetflixController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManageMemberController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HashtagController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('/register', function () {
//     return redirect()->route('login');
// });

Route::middleware('auth')->group(function () {
    // Route::get('/', function(){ return view('welcome'); });
    Route::get('/', [App\Http\Controllers\PartnerController::class, 'index'])->name('partner.index');
});

require __DIR__.'/auth.php';

