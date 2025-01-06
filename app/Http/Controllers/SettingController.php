<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Affiliate;
use App\Models\Popup;
use App\Models\Level;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $setting = Setting::first();
        return view('setting.index',compact('setting'));
    }

    public function affiliate()
    {
        $affiliate = Affiliate::first();
        return view('setting.af',compact('affiliate'));
    }

    public function popup()
    {
        $list = Popup::all();
        return view('setting.popup',compact('list'));
    }

    public function level()
    {
        $list = Level::orderBy('rank', 'asc')->get();
        return view('setting.level',compact('list'));
    }

    public function popup_create(Request $request)
    {   

        $page = array();
        if(isset($request->allpage)){
            array_push($page,$request->allpage);
        }
        if(isset($request->homepage)){
            array_push($page,$request->homepage);
        }
        if(isset($request->promotionpage)){
            array_push($page,$request->promotionpage);
        }
        if(isset($request->gamepage)){
            array_push($page,$request->gamepage);
        }
        // dd((isset($request->active) ? 1 : 0));
        $popup = new Popup;
        $popup->show_page = implode(',',$page);
        $popup->note = $request->note;
        $popup->active = (isset($request->active) ? 1 : 0);
        if($request->image){
            $fileName = rand().'.'.$request->image->extension();
            $request->image->move(public_path('images/popup'), $fileName);
            $popup->image = "/images/popup/".$fileName;
        }
        $popup->save();
        return redirect()->route('setting.popup')->with('status','success');
    }

    public function level_create(Request $request)
    {   

        $level = new Level;
        $level->level_name = $request->level_name;
        $level->level_min_point = $request->level_min_point;
        $level->level_max_point = $request->level_max_point;
        $level->rank = $request->rank;
        if($request->image){
            $fileName = rand().'.'.$request->image->extension();
            $request->image->move(public_path('images/level'), $fileName);
            $level->image = "/images/level/".$fileName;
        }
        $level->save();
        return redirect()->route('setting.level')->with('status','success');
    }

    public function level_update(Request $request)
    {   

        $level = Level::find($request->id);
        $level->level_name = $request->level_name;
        $level->level_min_point = $request->level_min_point;
        $level->level_max_point = $request->level_max_point;
        $level->rank = $request->rank;
        if($request->image){
            $fileName = rand().'.'.$request->image->extension();
            $request->image->move(public_path('images/level'), $fileName);
            $level->image = "/images/level/".$fileName;
        }
        $level->save();
        return redirect()->route('setting.level')->with('status','success');
    }

    public function level_destroy(Request $request)
    {   
        $level = Level::find($request->id);
        $level->delete();
        return redirect()->route('setting.level')->with('status','success');
    }



    public function affiliate_deposit_update(Request $request){
        // dd($request);
        $affiliate = Affiliate::first();
        $affiliate->af_min_deposit = $request->af_min_deposit;
        $affiliate->af_deposit_receive_lv_1 = $request->af_deposit_receive_lv_1;
        $affiliate->af_deposit_receive_lv_2 = $request->af_deposit_receive_lv_2;
        $affiliate->af_deposit_type = $request->af_type;
        $affiliate->af_max_receive_deposit_percent = $request->af_max_receive_percent;
        $affiliate->af_max_receive_deposit_baht = $request->af_max_receive_baht;
        $affiliate->is_enable_af_deposit = (isset($request->is_enable_af_deposit) ? 1 : 0);
        $affiliate->save();

        return redirect()->route('setting.affiliate')->with('status','success');
    }

    public function affiliate_winlose_update(Request $request){
        // dd($request);
        $affiliate = Affiliate::first();
        $affiliate->af_receive_percent_winlose_2 = $request->af_receive_percent_winlose_2;
        $affiliate->af_receive_percent_winlose_3 = $request->af_receive_percent_winlose_3;
        $affiliate->af_receive_percent_winlose_1 = $request->af_receive_percent_winlose_1;
        $affiliate->is_enable_af_winlose = (isset($request->is_enable_af_winlose) ? 1 : 0);
        $affiliate->save();

        return redirect()->route('setting.affiliate')->with('status','success');
    }

    public function deposit(Request $request){
        $setting = Setting::first();
        $setting->min_deposit = $request->min_deposit;
        $setting->is_enable_min_deposit = (isset($request->is_enable_min_deposit) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.index')->with('status','success');
    }

    public function withdraw(Request $request){
        $setting = Setting::first();
        $setting->min_withdraw = $request->min_withdraw;
        $setting->auto_min_withdraw = $request->auto_min_withdraw;
        $setting->is_enable_auto_withdraw = (isset($request->is_enable_auto_withdraw) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.index')->with('status','success');
    }

    public function cashback(Request $request){
        $setting = Setting::first();
        $setting->cashback_percent = $request->cashback_percent;
        $setting->cashback_turnover = $request->cashback_turnover;
        $setting->cashback_min_withdraw = $request->cashback_min_withdraw;
        $setting->is_enable_cashback = (isset($request->is_enable_cashback) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.index')->with('status','success');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('setting.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function maintenance(Request $request)
    {
        //
        $maintenance = 0;
        if(isset($request->ismaintence)){
            $maintenance = 1;
        }
        $setting = Setting::first();
        $setting->maintenance = $maintenance;
        $setting->save();

        return redirect()->route('setting.index')->with('status','success');
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
