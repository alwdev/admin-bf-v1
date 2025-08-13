<?php

use Illuminate\Support\Facades\Route;


Route::get('/login',function(){
    return view('partner.login');

});
Route::post('/partnerlogin', [App\Http\Controllers\PartnerController::class, 'store'])->name('partner.login');
Route::get('/partner/member-winloss', [App\Http\Controllers\PartnerController::class, 'getMemberWinlossData'])->name('partner.member.winloss.data');

Route::get('/', [App\Http\Controllers\PartnerController::class, 'index'])->name('home');

require __DIR__.'/auth.php';


Route::get('lang', [App\Http\Controllers\LanguageController::class, 'change'])->name("change.lang");
