<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManageMemberController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard_date'])->name('dashboard_date');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Manage member
    Route::get('/managemember', [ManageMemberController::class, 'index'])->name('managemember.index')->middleware('CheckPermissionUser:member,view');
    Route::get('/historyTransfer/{id}', [ManageMemberController::class, 'historyTransfer'])->name('managemember.historyTransfer')->middleware('CheckPermissionUser:transfer,view');
    Route::post('/approveDeposit', [ManageMemberController::class, 'approveDeposit'])->name('managemember.approveDeposit')->middleware('CheckPermissionUser:transfer,edit');
    Route::post('/memberchangepass', [ManageMemberController::class, 'changePassword'])->name('managemember.changePassword')->middleware('CheckPermissionUser:member,edit');
    Route::post('/memberlock', [ManageMemberController::class, 'memberlock'])->name('managemember.memberlock')->middleware('CheckPermissionUser:member,edit');
    Route::post('/memberdelete', [ManageMemberController::class, 'memberdelete'])->name('managemember.memberdelete')->middleware('CheckPermissionUser:member,edit');
    Route::post('/memberEditBalance', [ManageMemberController::class, 'memberEditBalance'])->name('managemember.memberEditBalance')->middleware('CheckPermissionUser:member,edit');
    Route::get('/getcashback', [ManageMemberController::class, 'cash_back'])->name('managemember.getcashback')->middleware('CheckPermissionUser:member,view');


    //Transaction
    Route::get('/transaction', [TransactionController::class, 'index'])->name('managemember.transaction')->middleware('CheckPermissionUser:transfer,view');

     //Manage user
    Route::get('/manageuser', [ManageUserController::class, 'index'])->name('manageuser.index')->middleware('CheckPermissionUser:manageuser,view');
    Route::get('/addnewuser', [ManageUserController::class, 'create'])->name('manageuser.addnewuser')->middleware('CheckPermissionUser:manageuser,edit');
    Route::post('/newuser', [ManageUserController::class, 'store'])->name('manageuser.store')->middleware('CheckPermissionUser:manageuser,edit');
    Route::post('/checkPassword', [ManageUserController::class, 'checkPassword'])->name('manageuser.checkPassword');
    Route::post('/changePassword', [ManageUserController::class, 'changePassword'])->name('manageuser.changePassword');
    Route::get('/setPermission/{id}', [ManageUserController::class, 'show'])->name('manageuser.show');
    Route::post('/setPermission', [ManageUserController::class, 'setPermission'])->name('manageuser.setPermission');
    Route::post('/updateuser', [ProfileController::class, 'updateuser'])->name('manageuser.updateuser');
    Route::post('/deluser', [ManageUserController::class, 'deluser'])->name('manageuser.deluser');


    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/maintenance', [SettingController::class, 'maintenance'])->name('setting.maintenance');

    //Report
    Route::get('/memberplay', [ReportController::class, 'member_play'])->name('report.memberplay')->middleware('CheckPermissionUser:report,view');
    Route::get('/memberplay_v2', [ReportController::class, 'member_play_v2'])->name('report.memberplay_v2')->middleware('CheckPermissionUser:report,view');
    Route::get('/memberplay_byprovider', [ReportController::class, 'member_play_callback'])->name('report.memberplay_byprovider')->middleware('CheckPermissionUser:report,view');
    Route::get('/edit_balance', [ReportController::class, 'edit_balance'])->name('report.edit_balance')->middleware('CheckPermissionUser:report,view');
    Route::get('/trancount', [ReportController::class, 'count_last_tranfer'])->name('report.count_last_tranfer')->middleware('CheckPermissionUser:report,view');
    Route::get('/get_round_by_date/{date}/{page}/{limit}', [App\Http\Controllers\ReportController::class, 'get_round_by_date'])->name('report.getRoundByDate');
    Route::get('/replay/{username}/{productId}/{betId}', [HistoryController::class, 'QueryReplay'])->name('report.QueryReplay')->middleware('CheckPermissionUser:report,view');

    Route::get('/list_memberplay/{date_id}/{dateend}', [ReportController::class, 'list_member_play'])->name('report.list_memberplay')->middleware('CheckPermissionUser:report,view');
    Route::get('/memberplay_name/{username}/{date_id}', [ReportController::class, 'member_play_name'])->name('report.memberplay_name')->middleware('CheckPermissionUser:report,view');
    Route::get('/sumtrans/{date_id}', [ReportController::class, 'sum_trans'])->name('report.sum_trans')->middleware('CheckPermissionUser:report,view');

    Route::get('/member_play_casino/{date_id}', [ReportController::class, 'member_play_casino'])->name('report.member_play_casino')->middleware('CheckPermissionUser:report,view');
    Route::get('/member_play_sport/{date_id}', [ReportController::class, 'member_play_sport'])->name('report.member_play_sport')->middleware('CheckPermissionUser:report,view');
    Route::get('/member_play_egame/{date_id}', [ReportController::class, 'member_play_egame'])->name('report.member_play_egame')->middleware('CheckPermissionUser:report,view');


    //bank account
    Route::get('/bankaccount', [App\Http\Controllers\BankAccountController::class, 'index'])->name('bankaccount.index');
    Route::get('/bankaccount/create', [App\Http\Controllers\BankAccountController::class, 'create'])->name('bankaccount.create');
    Route::post('/bankaccount/insert', [App\Http\Controllers\BankAccountController::class, 'store'])->name('bankaccount.store');
    Route::get('/bankaccount/edit/{id}', [App\Http\Controllers\BankAccountController::class, 'show'])->name('bankaccount.show');
    Route::post('/bankaccount/update/{id}', [App\Http\Controllers\BankAccountController::class, 'update'])->name('bankaccount.update');
    Route::post('/bankaccount/delete', [App\Http\Controllers\BankAccountController::class, 'destroy'])->name('bankaccount.destroy');

    //promotion
    Route::get('/promotion', [App\Http\Controllers\PromotionController::class, 'index'])->name('promotion.index');
    Route::get('/promotion/create', [App\Http\Controllers\PromotionController::class, 'create'])->name('promotion.create');
    Route::post('/promotion/store', [App\Http\Controllers\PromotionController::class, 'store'])->name('promotion.store');
    Route::get('/promotion/edit/{id}', [App\Http\Controllers\PromotionController::class, 'edit'])->name('promotion.edit');
    Route::post('/promotion/update/{id}', [App\Http\Controllers\PromotionController::class, 'update'])->name('promotion.update');
    Route::post('/promotion/delete', [App\Http\Controllers\PromotionController::class, 'destroy'])->name('promotion.destroy');

    Route::get('/ads', [App\Http\Controllers\PromotionAdsController::class, 'index'])->name('promotion_ads.index');
    Route::get('/ads/create', [App\Http\Controllers\PromotionAdsController::class, 'create'])->name('promotion_ads.create');
    Route::post('/ads/store', [App\Http\Controllers\PromotionAdsController::class, 'store'])->name('promotion_ads.store');
    Route::get('/ads/edit/{id}', [App\Http\Controllers\PromotionAdsController::class, 'edit'])->name('promotion_ads.edit');
    Route::post('/ads/update/{id}', [App\Http\Controllers\PromotionAdsController::class, 'update'])->name('promotion_ads.update');
    Route::post('/ads/delete', [App\Http\Controllers\PromotionAdsController::class, 'destroy'])->name('promotion_ads.destroy');

    Route::get('/provider', [App\Http\Controllers\ProviderController::class, 'index'])->name('provider.index');
    Route::get('/updateprovider', [App\Http\Controllers\ProviderController::class, 'updateprovider'])->name('provider.updateprovider');
    Route::get('/gamelist/{pid}', [App\Http\Controllers\ProviderController::class, 'gamelist'])->name('gamelist.index');
    Route::get('/updategamestatus', [App\Http\Controllers\ProviderController::class, 'updategamestatus'])->name('gamelist.updategamestatus');
    Route::post('/updategameimage', [App\Http\Controllers\ProviderController::class, 'updategameimage'])->name('gamelist.updategameimage');
    Route::post('/updateproviderimage', [App\Http\Controllers\ProviderController::class, 'updateproviderimage'])->name('provider.updateproviderimage');

    Route::get('/GetAllGame', [App\Http\Controllers\ProviderController::class, 'GetAllGame']);
});

require __DIR__.'/auth.php';

Route::get('/sync_history', [App\Http\Controllers\HistoryController::class, 'sync_history'])->name('sync_history');
Route::get('/get_biggame', [App\Http\Controllers\HistoryController::class, 'get_biggame'])->name('get_biggame');
Route::get('/get_common', [App\Http\Controllers\HistoryController::class, 'get_common'])->name('get_common');
Route::get('/get_supergame/{limit}', [App\Http\Controllers\HistoryController::class, 'get_supergame'])->name('get_supergame');
Route::get('/get_SAhistory', [App\Http\Controllers\HistoryController::class, 'get_supergame'])->name('get_supergame');
Route::get('/get_Sexyhistory', [App\Http\Controllers\HistoryController::class, 'get_supergame'])->name('get_supergame');
Route::get('/get_WMhistory', [App\Http\Controllers\HistoryController::class, 'get_supergame'])->name('get_supergame');

Route::get('/get_cashback', [ManageMemberController::class, 'cash_back']);
Route::get('/get_affiliate', [ManageMemberController::class, 'affiliate']);


Route::get('/QueryBetRecordsV2', [ReportController::class, 'QueryBetRecordsV2']);
