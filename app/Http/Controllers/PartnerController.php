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
use Carbon\Carbon;

class PartnerController extends Controller
{
    public function store(Request $request)
    {
        error_log('store ' . $request->email);
        error_log('store ' . $request->password);

        $user = Partner::where('contact_email', $request->email)->where('slug_name', $request->password)->first();

        if ($user) {
            error_log('partner found');
            session()->put('user', $user);
            return redirect('/');
        } else {
            error_log('partner not found');
            return back()->withErrors(['login' => 'Invalid email or password.']);
        }
    }

    public function add()
    {
        $code = $this->RandomString(10);
        return view('partner.add', compact('code'));
    }

    public function edit($id)
    {
        $data = Partner::find($id);
        return view('partner.edit', compact('data'));
    }



    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'contact_name' => ['required', 'string', 'max:255'],
            'slug_name' => ['required', 'string', Rule::unique('partner')->ignore($request->id)],
            'rate' => ['required'],
        ]);

        $partner = Partner::find($request->id);
        $partner->slug_name = $request->slug_name;
        $partner->url = env('APP_FRONT_URL') . '/register?partner=' . $request->slug_name;
        $partner->contact_name = $request->contact_name;
        $partner->contact_phonenumber = $request->contact_phonenumber;
        $partner->contact_email = $request->contact_email;
        $partner->rate = $request->rate;
        $partner->save();

        return redirect()->route('partner.index')->with('status', 'Partner added successfully');
    }

    function RandomString($length)
    {
        $original_string = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $original_string = implode('', $original_string);
        return substr(str_shuffle($original_string), 0, $length);
    }

    public function index()
    {
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        $partner = Partner::find(session('user')->id);
        $data = PartnerCommission::where('partner_id', session('user')->id)->get();

        $memberwinloss = $this->partner_call_winlose($partner);

        return view('partner.report', compact('data', 'partner','memberwinloss'));
    }

    function partner_call_winlose($partner)
    {
        set_time_limit(3000000000);
        $commissions = PartnerCommission::where('partner_id', $partner->id)->get();

        $members = [];

        foreach ($commissions as $value) {
            sleep(2);
            $total_commission = 0;

            $dates = explode('-', $value->note, 4);
            $date1 = $dates[0] . '-' . $dates[1] . '-' . $dates[2];
            $date2 = $dates[3];

            // สร้าง Carbon object สำหรับวันที่ปัจจุบัน
            $now = Carbon::now();

            // --- การคำนวณสำหรับ $date1 ---
            // สร้าง Carbon object จาก $date1
            $targetDate1 = Carbon::parse($date1);
            // คำนวณส่วนต่างของวันโดยให้ผลลัพธ์เป็นค่าลบถ้าอยู่ในอดีต (false)
            $diff1 = $now->diffInDays($targetDate1, false);

            // --- การคำนวณสำหรับ $date2 ---
            // สร้าง Carbon object จาก $date2
            $targetDate2 = Carbon::parse($date2);
            // คำนวณส่วนต่างของวันโดยให้ผลลัพธ์เป็นค่าลบถ้าอยู่ในอดีต (false)
            $diff2 = $now->diffInDays($targetDate2, false);

            $membersCount = 0;
            if ($partner->members !== null) {
                $membersCount = count(json_decode($partner->members));
            }

            if (json_decode($partner->members)) {
                set_time_limit(3000000000);
                foreach (json_decode($partner->members) as $_member) {
                    sleep(3);
                    $under_member = Members::where('id', $_member)->first();

                    try {
                        $bf_total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username, $diff1, $diff2);
                        if ($bf_total_bet) {
                            $total_bet = $bf_total_bet->valid_amount;
                            $winlose = $bf_total_bet->winloss;
                        } else {
                        }
                    } catch (\Exception $e) {

                        dd('Betflix API Error : '.$e->getMessage());
                        $total_bet = 0;
                        $winlose = 0;
                        continue;
                    }

                    try {
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username, $diff1, $diff2);

                        if (count($pg_total_bet['data']) > 0) {
                            $total_bet = $total_bet + $pg_total_bet['data'][0]['totalAmount'];
                        } else {
                        }
                    } catch (\Exception $e) {
                        dd('PgHard API Error : '.$e->getMessage());
                        $pg_total_bet = 0;
                    }

                    if($total_bet > 1){
                    $commission = abs($winlose) * ($partner->rate/ 100);
                    $total_commission += $commission;

                    $members2 = [
                        'total_bet' => $total_bet,
                        'winlose' => $winlose,
                        'rate' => $partner->rate,
                        'partner_id' => $partner->id,
                        'member_id' => $under_member->id,
                        'member_username' => $under_member->username,
                        'date1' => $date1,
                        'date2' => $date2,
                        'total_commission' => $total_commission,
                    ];
                    array_push($members, $members2);
                }
                }
            }
        }

        return $members;
    }
}
