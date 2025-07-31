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
    //
    public function index(){
        $list = Partner::all();
        // foreach ($list as $key => $value) {
        //     # code...
        //     $transfer = Transfer::whereIn()
        // }

        return view('partner.index',compact('list'));
    }

    public function add(){
        $code = $this->RandomString(10);
        return view('partner.add',compact('code'));
    }

    public function edit($id){
        $data = Partner::find($id);
        return view('partner.edit',compact('data'));
    }

    public function report($id){
        $partner = Partner::find($id);
        $data = PartnerCommission::where('partner_id',$id)->get();
        return view('partner.report',compact('data', 'partner'));
    }

    public function create(Request $request){

        $request->validate([
            'contact_name' => ['required','string','max:255'],
            'slug_name' => ['required','string','unique:partner'],
            'rate' => ['required'],
        ]);

        $partner = new Partner;
        $partner->slug_name = $request->slug_name;
        $partner->url = env('APP_FRONT_URL').'/register?partner='.$request->slug_name;
        $partner->contact_name = $request->contact_name;
        $partner->contact_phonenumber = $request->contact_phonenumber;
        $partner->contact_email = $request->contact_email;
        $partner->rate = $request->rate;
        $partner->save();

        return redirect()->route('partner.index')->with('status','Partner added successfully');
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

    function partner_call_winlose(){
        set_time_limit(3000000000);
        Log::info("Run Check");
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT เริ่มทำการ ส่วนแบ่ง Partner')
        ->send();



        $partners = Partner::all();
        Log::info("Total Partner  : ".count($partners));
        foreach ($partners as $value) {
            sleep(2);
            $total_commission = 0;
            // Check if $value->members is not null before attempting to decode and count
            $membersCount = 0;
            if ($value->members !== null) {
                $membersCount = count(json_decode($value->members));
            }

            Log::info("Member main : " . $value->contanct_name . ' under partner count = ' . $membersCount);
            if(json_decode($value->members)){
                set_time_limit(3000000000);
                foreach(json_decode($value->members) as $_member){
                    sleep(3);

                    $under_member = Members::where('id',$_member)->first();
                    Log::info("Under of ".$value->contanct_name." member : " .$under_member->username);

                    try{
                        $bf_total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username,-1,-1);
                        if($bf_total_bet){
                            $total_bet = $bf_total_bet->valid_amount;
                            $winlose = $bf_total_bet->winloss;
                            Log::info("bf_total_bet : " . $bf_total_bet->valid_amount);
                        }else{
                            Log::info("bf_total_bet : " . $bf_total_bet->msg);
                        }
                    } catch (\Exception $e) {
                        Log::info('Betflix API Error : '.$e->getMessage());
                        $total_bet =0;
                        $winlose =0;
                        continue;
                    }

                    try{
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username,-1,-1);

                        if(count($pg_total_bet['data']) > 0){
                            Log::info("pg_total_bet : " . $pg_total_bet['data'][0]['totalAmount']);
                            $total_bet = $total_bet + $pg_total_bet['data'][0]['totalAmount'];
                        }else{
                            Log::info('PgHard API No have User Data '.$under_member->username);
                        }
                    } catch (\Exception $e) {
                        Log::info('PgHard API Error : '.$e->getMessage());
                        $pg_total_bet =0;
                    }

                    Log::info("total_bet : ".$total_bet);

                    if($total_bet > 1){

                        $commission = abs($winlose) * ($value->rate/ 100);
                        $total_commission += $commission;
                        Log::info("commission ยอด winlose : ".$winlose." commission : ".$commission);

                    }
                }
                if($total_commission > 0){
                    $value->total_profit = $value->total_profit + $total_commission;
                    $value->save();

                    $start_date=date('Y-m-d',strtotime('-1 day'));
                    $end_date=date('Y-m-d',strtotime('-1 day'));
                    PartnerCommission::create([
                        'partner_id' => $value->id,
                        'amount' => $value->amount,
                        'payment_type' => $value->payment_type,
                        'payment_status' => 'pending',
                        'transaction_id' =>'',
                        'note' => $start_date . '-' . $end_date // Corrected line
                    ]);
                }

                // $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($value->username,floor($commission));
                // Log::info('Deposit commission to Betflix  '.$bf_deposit.' '.floor($commission).' User =  '.$value->username);
                // $value->wallet_balance = (float) ($value->wallet_balance + $commission);
                // $value->save();
            }

        }
        Log::info('success Run ส่วนแบ่ง Partner');
        // TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        //     ->line(env('APP_NAME'))
        //     ->line('BOT สิ้นสุดการ Run ส่วนแบ่ง Partner ')
        //     ->send();
        return 'success';
    }

}
