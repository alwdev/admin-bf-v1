<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Affiliate;

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
