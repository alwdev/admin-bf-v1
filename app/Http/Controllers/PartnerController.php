<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;
use App\Models\PromotionUsed;
use App\Models\Affiliate;
use App\Models\WheelSpin;
use App\Models\Setting;
use App\Models\Members;
use App\Models\PartnerCommission;
use NotificationChannels\Telegram\TelegramMessage;


class PartnerController extends Controller
{


    public function add(){
        $code = $this->RandomString(10);
        return view('partner.add',compact('code'));
    }

    public function edit($id){
        $data = Partner::find($id);
        return view('partner.edit',compact('data'));
    }

    public function index(){

        $partner = Partner::find(auth()->user()->id);
        $data = PartnerCommission::where('partner_id',auth()->user()->id)->get();
        return view('partner.report',compact('data', 'partner'));
    }



    public function update(Request $request){

        $request->validate([
            'id' => ['required'],
            'contact_name' => ['required','string','max:255'],
            'slug_name' => ['required','string',Rule::unique('partner')->ignore($request->id)],
            'rate' => ['required'],
        ]);

        $partner = Partner::find($request->id);
        $partner->slug_name = $request->slug_name;
        $partner->url = env('APP_FRONT_URL').'/register?partner='.$request->slug_name;
        $partner->contact_name = $request->contact_name;
        $partner->contact_phonenumber = $request->contact_phonenumber;
        $partner->contact_email = $request->contact_email;
        $partner->rate = $request->rate;
        $partner->save();

        return redirect()->route('partner.index')->with('status','Partner added successfully');
    }

    function RandomString($length) {
        $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
        $original_string = implode("", $original_string);
        return substr(str_shuffle($original_string), 0, $length);
    }


}
