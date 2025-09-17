<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Affiliate;
use App\Models\Popup;
use App\Models\Level;
use App\Models\Ranking;
use App\Models\Members;
use App\Models\Coupon;
use App\Models\WheelSpin;
use App\Models\SystemAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $setting = Setting::first();
        return view('setting.index', compact('setting'));
    }

    public function affiliate()
    {
        $affiliate = Affiliate::first();
        return view('setting.af', compact('affiliate'));
    }

    public function popup()
    {
        $list = Popup::all();
        return view('setting.popup', compact('list'));
    }

    public function coupon()
    {
        $list = Coupon::all();
        return view('setting.coupon', compact('list'));
    }

    public function level()
    {
        $list = Level::orderBy('rank', 'asc')->get();
        return view('setting.level', compact('list'));
    }

    public function mission()
    {
        $setting = Setting::first();
        return view('setting.mission', compact('setting'));
    }

    public function ranking()
    {
        $list = Ranking::orderBy('rank', 'asc')->get();
        return view('setting.ranking', compact('list'));
    }

    public function deposit_continuously()
    {
        $setting = Setting::first();
        return view('setting.continuously', compact('setting'));
    }

    public function point()
    {
        $setting = Setting::first();
        return view('setting.point', compact('setting'));
    }

    public function coupon_create(Request $request)
    {
        $request->validate([
            'coupon' => ['required'],
        ]);
        $coupon = new Coupon;
        $coupon->coupon = $request->coupon;
        $coupon->max = $request->max;
        $coupon->amount = $request->amount;
        $coupon->date_start = $request->date_start . ' ' . $request->time_start;
        $coupon->date_end = $request->date_end . ' ' . $request->time_end;
        $coupon->user_id = auth()->user()->id;
        $coupon->enable = (isset($request->enable) ? 1 : 0);
        $coupon->active = 1;
        $coupon->save();

        return redirect()->route('setting.coupon')->with('status', 'success');
    }

    public function coupon_update(Request $request)
    {
        $request->validate([
            'coupon' => ['required'],
        ]);

        $coupon = Coupon::find($request->id);
        $coupon->coupon = $request->coupon;
        $coupon->max = $request->max;
        $coupon->amount = $request->amount;
        $coupon->date_start = $request->date_start . ' ' . $request->time_start;
        $coupon->date_end = $request->date_end . ' ' . $request->time_end;
        $coupon->user_id = auth()->user()->id;
        $coupon->enable = (isset($request->enable) ? 1 : 0);
        $coupon->active = 1;
        $coupon->save();

        return redirect()->route('setting.coupon')->with('status', 'success');
    }

    public function coupon_update_status(Request $request)
    {

        $coupon = Coupon::find($request->id);
        $coupon->enable = $request->enable;
        $coupon->save();

        return 1;
    }

    public function popup_create(Request $request)
    {

        $page = array();
        if (isset($request->allpage)) {
            array_push($page, $request->allpage);
        }
        if (isset($request->homepage)) {
            array_push($page, $request->homepage);
        }
        if (isset($request->promotionpage)) {
            array_push($page, $request->promotionpage);
        }
        if (isset($request->gamepage)) {
            array_push($page, $request->gamepage);
        }
        // dd((isset($request->active) ? 1 : 0));
        $popup = new Popup;
        $popup->show_page = implode(",", $request->input('show_page', []));
        $popup->note = $request->note;
        $popup->active = (isset($request->active) ? 1 : 0);

        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/popup'), $fileName);
            $popup->image = "/images/popup/" . $fileName;
        }

        $popup->save();
        return redirect()->route('setting.popup')->with('status', 'success');
    }

    public function popup_update(Request $request)
    {
        $popup = Popup::findOrFail($request->id);

        // ✅ เช็คว่ามีไฟล์ใหม่ไหม
        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/popup'), $fileName);
            $popup->image = "/images/popup/" . $fileName;
        }

        $popup->show_page = implode(",", $request->input('show_page', []));
        $popup->note = $request->note;
        $popup->active = $request->has('active') ? 1 : 0;
        $popup->save();

        return redirect()->route('setting.popup')->with('status', 'success');
    }

    public function popup_delete(Request $request)
    {
        $popup = Popup::find($request->id);
        if (!$popup) {
            return response()->json(['success' => false, 'message' => 'ไม่พบป๊อบอัพนี้!']);
        }

        $popup->delete(); // ลบจากฐานข้อมูล
        return response()->json(['success' => true]);
    }


    public function level_create(Request $request)
    {

        $level = new Level;
        $level->level_name = $request->level_name;
        $level->level_min_point = $request->level_min_point;
        $level->level_max_point = $request->level_max_point;
        $level->rank = $request->rank;
        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/level'), $fileName);
            $level->image = "/images/level/" . $fileName;
        }
        $level->save();
        return redirect()->route('setting.level')->with('status', 'success');
    }

    public function level_update(Request $request)
    {

        $level = Level::find($request->id);
        $level->level_name = $request->level_name;
        $level->level_min_point = $request->level_min_point;
        $level->level_max_point = $request->level_max_point;
        $level->rank = $request->rank;
        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/level'), $fileName);
            $level->image = "/images/level/" . $fileName;
        }
        $level->save();
        return redirect()->route('setting.level')->with('status', 'success');
    }

    public function level_destroy(Request $request)
    {
        $level = Level::find($request->id);
        $level->delete();
        return redirect()->route('setting.level')->with('status', 'success');
    }


    public function ranking_create(Request $request)
    {
        $ranking = new Ranking;
        $ranking->credit = $request->credit;
        $ranking->diamond = $request->diamond;
        $ranking->exp = $request->exp;
        $ranking->rank = $request->rank;
        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/ranking'), $fileName);
            $ranking->image = "/images/ranking/" . $fileName;
        }
        $ranking->save();
        return redirect()->route('setting.ranking')->with('status', 'success');
    }

    public function ranking_update(Request $request)
    {

        $ranking = Ranking::find($request->id);
        $ranking->credit = $request->credit;
        $ranking->diamond = $request->diamond;
        $ranking->exp = $request->exp;
        $ranking->rank = $request->rank;
        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/ranking'), $fileName);
            $ranking->image = "/images/ranking/" . $fileName;
        }
        $ranking->save();
        return redirect()->route('setting.ranking')->with('status', 'success');
    }

    public function ranking_destroy(Request $request)
    {
        $ranking = Ranking::find($request->id);
        $ranking->delete();
        return redirect()->route('setting.ranking')->with('status', 'success');
    }



    public function affiliate_deposit_update(Request $request)
    {
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

        return redirect()->route('setting.affiliate')->with('status', 'success');
    }

    public function affiliate_winlose_update(Request $request)
    {
        // dd($request);
        $affiliate = Affiliate::first();
        $affiliate->af_receive_percent_winlose_2 = $request->af_receive_percent_winlose_2;
        $affiliate->af_receive_percent_winlose_3 = $request->af_receive_percent_winlose_3;
        $affiliate->af_receive_percent_winlose_1 = $request->af_receive_percent_winlose_1;
        $affiliate->is_enable_af_winlose = (isset($request->is_enable_af_winlose) ? 1 : 0);
        $affiliate->save();

        return redirect()->route('setting.affiliate')->with('status', 'success');
    }

    public function deposit_continuously_update(Request $request)
    {
        $setting = Setting::first();
        $setting->continuously_receive = $request->continuously_receive;
        $setting->continuously_login = $request->continuously_login;
        $setting->continuously_min_deposit = $request->continuously_min_deposit;
        $setting->is_enable_continuously = (isset($request->is_enable_continuously) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.deposit_continuously')->with('status', 'success');
    }

    public function point_update(Request $request)
    {
        $setting = Setting::first();
        $setting->point = $request->point;
        $setting->turnover_point = $request->turnover_point;
        $setting->is_enable_point = (isset($request->is_enable_point) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.point')->with('status', 'success');
    }

    public function mission_deposit_update(Request $request)
    {
        $setting = Setting::first();
        $setting->mission_deposit_goal = $request->mission_deposit_goal;
        $setting->mission_deposit_point = $request->mission_deposit_point;
        $setting->mission_deposit_credit = $request->mission_deposit_credit;
        $setting->is_enable_mission_deposit = (isset($request->is_enable_mission_deposit) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.mission')->with('status', 'success');
    }

    public function mission_play_update(Request $request)
    {
        $setting = Setting::first();
        $setting->mission_play_goal = $request->mission_play_goal;
        $setting->mission_play_point = $request->mission_play_point;
        $setting->mission_play_credit = $request->mission_play_credit;
        $setting->is_enable_mission_play = (isset($request->is_enable_mission_play) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.mission')->with('status', 'success');
    }

    public function mission_win_update(Request $request)
    {
        $setting = Setting::first();
        $setting->mission_win_goal = $request->mission_win_goal;
        $setting->mission_win_point = $request->mission_win_point;
        $setting->mission_win_credit = $request->mission_win_credit;
        $setting->is_enable_mission_win = (isset($request->is_enable_mission_win) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.mission')->with('status', 'success');
    }

    public function deposit(Request $request)
    {
        $setting = Setting::first();
        $setting->min_deposit = $request->min_deposit;
        $setting->is_enable_min_deposit = (isset($request->is_enable_min_deposit) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.index')->with('status', 'success');
    }

    public function withdraw(Request $request)
    {
        $setting = Setting::first();
        $setting->min_withdraw = $request->min_withdraw;
        $setting->auto_min_withdraw = $request->auto_min_withdraw;
        $setting->is_enable_auto_withdraw = (isset($request->is_enable_auto_withdraw) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.index')->with('status', 'success');
    }

    public function cashback(Request $request)
    {
        $setting = Setting::first();
        $setting->cashback_percent = $request->cashback_percent;
        $setting->cashback_turnover = $request->cashback_turnover;
        $setting->cashback_min_withdraw = $request->cashback_min_withdraw;
        $setting->is_enable_cashback = (isset($request->is_enable_cashback) ? 1 : 0);
        $setting->save();

        return redirect()->route('setting.index')->with('status', 'success');
    }


    public function wheel()
    {
        $setting = WheelSpin::first();
        return view('setting.wheel', compact('setting'));
    }

    public function wheel_update(Request $request)
    {
        // dd($request->win_1_reward);
        $data = WheelSpin::first();
        $data->ticket_condition = $request->ticket_condition;
        $data->limit_per_day = (isset($request->limit_per_day) ? $request->limit_per_da : 0);
        $data->limit_person = (isset($request->limit_person) ? $request->limit_person : 0);
        $data->enable = (isset($request->enable) ? 1 : 0);
        $data->limit_withdraw = $request->limit_withdraw;
        $data->trunover = (isset($request->trunover) ? $request->trunover : 0);
        $data->win_1_reward = $request->win_1_reward;
        $data->win_1 = $request->win_1;
        $data->win_1_rate = $request->win_1_rate;
        $data->win_2_reward = $request->win_2_reward;
        $data->win_2 = $request->win_2;
        $data->win_2_rate = $request->win_2_rate;
        $data->win_3_reward = $request->win_3_reward;
        $data->win_3 = $request->win_3;
        $data->win_3_rate = $request->win_3_rate;
        $data->win_4_reward = $request->win_4_reward;
        $data->win_4 = $request->win_4;
        $data->win_4_rate = $request->win_4_rate;
        $data->win_5_reward = $request->win_5_reward;
        $data->win_5 = $request->win_5;
        $data->win_5_rate = $request->win_5_rate;
        $data->win_6_reward = $request->win_6_reward;
        $data->win_6 = $request->win_6;
        $data->win_6_rate = $request->win_6_rate;
        $data->win_7_reward = $request->win_7_reward;
        $data->win_7 = $request->win_7;
        $data->win_7_rate = $request->win_7_rate;
        $data->win_8_reward = $request->win_8_reward;
        $data->win_8 = $request->win_8;
        $data->win_8_rate = $request->win_8_rate;
        $data->win_9_reward = $request->win_9_reward;
        $data->win_9 = $request->win_9;
        $data->win_9_rate = $request->win_9_rate;
        $data->win_10_reward = $request->win_10_reward;
        $data->win_10 = $request->win_10;
        $data->win_10_rate = $request->win_10_rate;
        $data->win_11_reward = $request->win_11_reward;
        $data->win_11 = $request->win_11;
        $data->win_11_rate = $request->win_11_rate;
        $data->win_12_reward = $request->win_12_reward;
        $data->win_12 = $request->win_12;
        $data->win_12_rate = $request->win_12_rate;

        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/wheel'), $fileName);
            $data->image = "/images/wheel/" . $fileName;
        }
        $data->save();

        return redirect()->route('setting.wheel')->with('status', 'success');
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
        if (isset($request->ismaintence)) {
            $maintenance = 1;
        }
        $setting = Setting::first();
        $setting->maintenance = $maintenance;
        $setting->save();

        return redirect()->route('setting.index')->with('status', 'success');
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

    public function regenmember_idxxx()
    {

        $members = Members::all();
        if ($members) {
            foreach ($members as $member) {
                $length = 6;
                $randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
                $member->member_id = env('BF_AGENT') . $randomletter;
                $member->save();
            }
        }
        echo "Successfully";
    }

    public function alert_index()
    {
        $list = SystemAlert::all();

        return view('setting.alert_index', compact('list'));
    }

    public function alert_del(Request $request)
    {
        $d = SystemAlert::find($request->id);
        if ($d) {
            $d->delete();
        }
        return back()->with('success', 'success');
    }

    public function alert_store(Request $request)
    {
        try {
            // 1. Validate the incoming request data
            $validatedData = $request->validate([
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'message' => 'required|string|max:255',
                'active' => 'required|boolean',
            ]);

            // 2. Use a database transaction
            DB::beginTransaction();

            // 3. Create the new alert record using the mass assignment method
            //    which is the standard Laravel way.
            SystemAlert::create([
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'message' => $validatedData['message'],
                'active' => (bool) $validatedData['active'], // Ensure 'active' is a boolean
            ]);

            DB::commit();

            Log::info('New alert created successfully.', ['data' => $validatedData]);
            return redirect()->route('setting.alert')->with('success', 'Alert created successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during alert creation.', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create new alert.', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update an existing alert message.
     */
    public function alert_update(Request $request)
    {
        try {
            // 1. Validate the incoming request data
            $validatedData = $request->validate([
                'id' => 'required|integer|exists:system_alert,id',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'message' => 'required|string|max:255',
                'active' => 'required|boolean',
            ]);

            // 2. Use a database transaction
            DB::beginTransaction();

            // 3. Find the alert by its ID and update it
            $alert = SystemAlert::findOrFail($validatedData['id']);
            $alert->update([
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'message' => $validatedData['message'],
                'active' => $validatedData['active'],
            ]);

            DB::commit();

            Log::info('Alert updated successfully.', ['id' => $alert->id, 'data' => $validatedData]);
            return redirect()->route('setting.alert')->with('success', 'Alert updated successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during alert update.', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update alert.', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
