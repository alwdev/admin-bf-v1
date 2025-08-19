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
        // ตรวจสอบ session เหมือนเดิม
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        // ดึงข้อมูลพื้นฐานที่ใช้แสดงผลในหน้าเว็บ
        $partner = Partner::find(session('user')->id);
        $data = PartnerCommission::where('partner_id', session('user')->id)->get();

        // **ไม่ต้องเรียก method partner_call_winlose() ตรงนี้**
        // เราจะไปเรียกข้อมูลนี้ด้วย AJAX ในภายหลัง

        // ส่งเฉพาะข้อมูลพื้นฐานไปยัง View
        return view('partner.report', compact('data', 'partner'));
    }

    function partner_call_winlose($partner)
    {
        // ไม่จำเป็นต้อง set_time_limit() หรือ sleep() หากโค้ดมีประสิทธิภาพ
        // ลบ set_time_limit(3000000000); ออก

        $commissions = PartnerCommission::where('partner_id', $partner->id)->get();
        $membersData = [];

        // ดึงข้อมูลสมาชิกทั้งหมดของ Partner มาเก็บไว้ก่อน
        $underMembers = [];
        if ($partner->members !== null) {
            $memberIds = json_decode($partner->members);
            // ใช้ in_where เพื่อดึงข้อมูลสมาชิกทั้งหมดในครั้งเดียว
            $underMembers = Members::whereIn('id', $memberIds)->get()->keyBy('id');
        }

        foreach ($commissions as $commission) {
            // ลบคำสั่ง sleep(2); ออก

            $dates = explode('-', $commission->note, 4);
            $date1 = $dates[0] . '-' . $dates[1] . '-' . $dates[2];
            $date2 = $dates[3];

            $now = Carbon::now();
            $targetDate1 = Carbon::parse($date1);
            $diff1 = $now->diffInDays($targetDate1, false);

            $targetDate2 = Carbon::parse($date2);
            $diff2 = $now->diffInDays($targetDate2, false);

            if ($underMembers->count() > 0) {
                // ลบ set_time_limit(3000000000); ออก
                foreach ($underMembers as $under_member) {
                    // ลบคำสั่ง sleep(3); ออก

                    $total_bet = 0;
                    $winlose = 0;

                    try {
                        // การเรียก API ภายนอกควรถูกย้ายไปทำใน Background Job หากมีจำนวนมาก
                        $bf_total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username, $diff1, $diff2);
                        if ($bf_total_bet) {
                            $total_bet += $bf_total_bet->valid_amount;
                            $winlose += $bf_total_bet->winloss;
                        }
                    } catch (\Exception $e) {
                        // ไม่ควรใช้ dd() ในโค้ดจริง
                        // ควรใช้การบันทึก Log แทน เช่น Log::error('Betflix API Error: ' . $e->getMessage());
                        continue;
                    }

                    try {
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username, $diff1, $diff2);
                        if (!empty($pg_total_bet['data']) && count($pg_total_bet['data']) > 0) {
                            $total_bet += $pg_total_bet['data'][0]['totalAmount'];
                        }
                    } catch (\Exception $e) {
                        // ไม่ควรใช้ dd() ในโค้ดจริง
                        // ควรใช้การบันทึก Log แทน
                    }

                    if ($total_bet > 1) {
                        $commission = 0;

                        // คำนวณเฉพาะกรณีที่ winlose เป็นค่าติดลบ
                        if ($winlose < 0) {
                            $commission = abs($winlose) * ($partner->rate / 100);
                        }

                        $membersData[] = [
                            'total_bet' => $total_bet,
                            'winlose' => $winlose,
                            'rate' => $partner->rate,
                            'partner_id' => $partner->id,
                            'member_id' => $under_member->id,
                            'member_username' => $under_member->username,
                            'date1' => $date1,
                            'date2' => $date2,
                            'total_commission' => $commission,
                        ];
                    }
                }
            }
        }
        return $membersData;
    }
    public function getMemberWinlossData(Request $request)
    {
        // ตรวจสอบสิทธิ์การเข้าถึง (ถ้าจำเป็น)
        if (!session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $partner = Partner::find(session('user')->id);
        $memberwinloss = $this->partner_call_winlose($partner);

        return response()->json($memberwinloss);
    }
}
