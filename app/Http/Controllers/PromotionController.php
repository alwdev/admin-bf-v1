<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use Illuminate\Support\Facades\Log; // Useful for debugging

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $promotions = Promotion::where('active', 1)->get();
        return view('promotion.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('promotion.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    //     $pro = new Promotion;
    //     $pro->name = $request->name;
    //     $pro->turnover = $request->turnover;
    //     $pro->deposit = $request->deposit;
    //     $pro->bonus = $request->bonus;
    //     if(isset($request->enable)){
    //         $pro->enable = $request->enable;
    //     }else{
    //         $pro->enable = 0;
    //     }
    //     if(isset($request->is_newuser)){
    //         $pro->is_newuser = $request->is_newuser;
    //     }else{
    //         $pro->is_newuser = 0;
    //     }
    //     $pro->description = $request->description;
    //     if($request->image){
    //         $fileName = time().'.'.$request->image->extension();
    //         $request->image->move('_image', $fileName);  ////  server public_html path
    //         $pro->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
    //     }
    //     $pro->withdraw_limit = $request->withdraw_limit;
    //     $pro->active = 1;
    //     $pro->save();
    //     return redirect()->route('promotion.index')->with('status','200');
    // }

    public function store(Request $request)
    {
        // 1. Validation Rules
        $rules = [
            'name' => 'required|string|max:255',
            'deposit' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,gif|max:2048', // 2MB
            'description' => 'nullable|string',

            // Checkbox fields
            'is_newuser' => 'nullable|boolean',
            'enable' => 'nullable|boolean',
            'is_percentage_based' => 'nullable|boolean',
            'is_recurring_promotion' => 'nullable|boolean',
            // 'is_first_deposit_bonus' => 'nullable|boolean', // Uncomment if you use this

            // Applicable Games
            'applicable_games' => 'required|array',
            'applicable_games.*' => 'in:ทั้งหมด,สล็อต,คาสิโนสด,ยิงปลา,เกมส์ไพ่,หวย,กีฬา',
        ];

        // Conditional Validation Rules for Amount vs Percentage fields
        if ($request->boolean('is_percentage_based')) {
            $rules['bonus_percentage'] = 'required|numeric|min:0|max:100';
            $rules['turnover_percentage'] = 'required|numeric|min:0'; // อนุญาตให้เกิน 100 เช่น 500 สำหรับ 5 เท่า
            $rules['withdraw_limit_percentage'] = 'required|numeric|min:0';

            // Make fixed amount fields nullable if percentage based
            $rules['bonus'] = 'nullable';
            $rules['turnover'] = 'nullable';
            $rules['withdraw_limit'] = 'nullable';
        } else {
            $rules['bonus'] = 'required|numeric|min:0';
            $rules['turnover'] = 'required|numeric|min:0';
            $rules['withdraw_limit'] = 'required|numeric|min:0';

            // Make percentage fields nullable if fixed amount based
            $rules['bonus_percentage'] = 'nullable';
            $rules['turnover_percentage'] = 'nullable';
            $rules['withdraw_limit_percentage'] = 'nullable';
        }

        // Conditional Validation for recurring_promotion_days AND recurring_bonus_percentage
        if ($request->boolean('is_recurring_promotion')) {
            $rules['recurring_promotion_days'] = 'required|integer|min:1';
            $rules['recurring_bonus_percentage'] = 'required|numeric|min:0|max:100'; // ถ้าโบนัสต่อเนื่องเป็น %

            // *** NEW: Validation for recurring_turnover fields ***
            // You might want to make these required_if 'is_recurring_promotion' is true
            // and maybe conditional based on a new checkbox in the UI if recurring_promotion also has percentage vs fixed
            // For now, let's assume if it's recurring, one of them must be set.
            $rules['recurring_turnover'] = 'nullable|numeric|min:0'; // Fixed turnover for recurring
            $rules['recurring_turnover_percentage'] = 'nullable|numeric|min:0'; // Percentage turnover for recurring

            // Example if you want one OR the other (not both) for recurring turnover
            $rules['recurring_turnover_percentage'] = 'required_without:recurring_turnover|nullable|numeric|min:0';

        } else {
            $rules['recurring_promotion_days'] = 'nullable';
            $rules['recurring_bonus_percentage'] = 'nullable';
            // *** NEW: Make recurring turnover fields nullable if not a recurring promotion ***
            $rules['recurring_turnover'] = 'nullable';
            $rules['recurring_turnover_percentage'] = 'nullable';
        }

        // Run Validation
        $request->validate($rules);

        $pro = new Promotion();
        $pro->name = $request->name;
        $pro->deposit = $request->deposit;
        $pro->description = $request->description;
        $pro->active = true; // Set to boolean true

        // 2. จัดการค่า Bonus, Turnover, Withdraw Limit (บาท/เท่า หรือ เปอร์เซ็นต์)
        $pro->is_percentage_based = $request->boolean('is_percentage_based'); // Assign this first

        if ($pro->is_percentage_based) {
            // ถ้าใช้เปอร์เซ็นต์
            $pro->bonus = 0.0;
            $pro->bonus_percentage = $request->bonus_percentage;

            $pro->turnover = 0.0;
            $pro->turnover_percentage = $request->turnover_percentage;

            $pro->withdraw_limit = 0.0;
            $pro->withdraw_limit_percentage = $request->withdraw_limit_percentage;
        } else {
            // ถ้าใช้ค่าเป็นบาท/เท่า
            $pro->bonus = $request->bonus;
            $pro->bonus_percentage = null;

            $pro->turnover = $request->turnover;
            $pro->turnover_percentage = null;

            $pro->withdraw_limit = $request->withdraw_limit;
            $pro->withdraw_limit_percentage = null;
        }

        // 3. จัดการ Checkbox fields (boolean)
        $pro->enable = $request->boolean('enable');
        $pro->is_newuser = $request->boolean('is_newuser');
        $pro->is_recurring_promotion = $request->boolean('is_recurring_promotion');
        // $pro->is_first_deposit_bonus = $request->boolean('is_first_deposit_bonus'); // Uncomment if you use this

        // 4. จัดการ recurring_promotion_days, recurring_bonus_percentage, AND NEW recurring_turnover fields
        if ($pro->is_recurring_promotion) { // Use $pro->is_recurring_promotion after assignment
            $pro->recurring_promotion_days = $request->recurring_promotion_days;
            $pro->recurring_bonus_percentage = $request->recurring_bonus_percentage;
            // *** NEW: Assign recurring turnover values ***
            $pro->recurring_turnover_percentage = $request->recurring_turnover_percentage;
        } else {
            $pro->recurring_promotion_days = null;
            $pro->recurring_bonus_percentage = null;
            // *** NEW: Set recurring turnover values to null if not a recurring promotion ***
            $pro->recurring_turnover_percentage = null;
        }

        // 5. จัดการ applicable_games
        $pro->applicable_games = $request->input('applicable_games');

        // 6. จัดการรูปภาพ
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('_image'), $fileName);
            $pro->image = env('APP_URL').'/_image/' . $fileName;
        } else {
            // Assign a default image path if no image is uploaded and it's required
            // (Your validation already makes it required, so this else block might not be hit)
            // It's safer to have a default image in case validation is bypassed or for existing records.
            $pro->image = env('APP_URL').'/_image/default_promotion.png'; // Make sure you have a default image
        }

        // 7. กำหนดค่าเริ่มต้นสำหรับคอลัมน์อื่นๆ ที่เป็น NOT NULL ใน DB และไม่มีในฟอร์ม
        // ตรวจสอบจาก image_1e613a.png
        // - store_id (bigint)
        // - withdraw_percent (int)

        // Ensure these are set if they are NOT NULL in your database and not explicitly from form
        // From image_10b2b5.png (Promotion Model screenshot), store_id has a default of 1, withdraw_percent has 0.
        // So, if your model's $attributes array correctly sets these defaults, you might not need this here.
        // However, if your DB columns are truly NOT NULL without a default, or you want to override model defaults,
        // then keep this logic.
        if (is_null($pro->store_id)) {
            $pro->store_id = 1; // กำหนดค่า default เช่น store แรก
        }

        if (is_null($pro->withdraw_percent)) {
            $pro->withdraw_percent = 0; // กำหนดค่า default เป็น 0
        }

        try {
            $pro->save();
            return redirect()->route('promotion.index')->with('status', '200');
        } catch (\Exception $e) {
            Log::error("Error saving promotion: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to save promotion. Please try again.']);
        }
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
        $promotion = Promotion::find($id);
        return view('promotion.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
    {
        // 1. ค้นหารายการ Promotion ที่มีอยู่
        $pro = Promotion::find($id);

        if (!$pro) {
            // จัดการกรณีที่ไม่พบโปรโมชั่น (เช่น Redirect พร้อมข้อความ Error)
            return redirect()->route('promotion.index')->with('error', 'Promotion not found!');
        }

        // 2. กำหนดกฎการตรวจสอบ (Validation Rules)
        $rules = [
            'name' => 'required|string|max:255',
            'deposit' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,gif|max:2048', // 2MB, เปลี่ยนเป็น nullable สำหรับการอัปเดต
            'description' => 'nullable|string',

            // Checkbox fields
            'is_newuser' => 'nullable|boolean',
            'enable' => 'nullable|boolean',
            'is_percentage_based' => 'nullable|boolean',
            'is_recurring_promotion' => 'nullable|boolean',
            'is_first_deposit_bonus' => 'nullable|boolean',

            // Applicable Games
            'applicable_games' => 'required|array',
            'applicable_games.*' => 'in:ทั้งหมด,สล็อต,คาสิโนสด,ยิงปลา,เกมส์ไพ่,หวย,กีฬา',
        ];

        // กฎการตรวจสอบแบบมีเงื่อนไขสำหรับ Amount vs Percentage fields
        if ($request->boolean('is_percentage_based')) {
            $rules['bonus_percentage'] = 'required|numeric|min:0|max:100';
            $rules['turnover_percentage'] = 'required|numeric|min:0';
            $rules['withdraw_limit_percentage'] = 'required|numeric|min:0|max:100';

            $rules['bonus'] = 'nullable'; // ทำให้ช่อง Amount เป็น nullable หากใช้ Percentage
            $rules['turnover'] = 'nullable';
            $rules['withdraw_limit'] = 'nullable';
        } else {
            $rules['bonus'] = 'required|numeric|min:0';
            $rules['turnover'] = 'required|numeric|min:0';
            $rules['withdraw_limit'] = 'required|numeric|min:0';

            $rules['bonus_percentage'] = 'nullable'; // ทำให้ช่อง Percentage เป็น nullable หากใช้ Amount
            $rules['turnover_percentage'] = 'nullable';
            $rules['withdraw_limit_percentage'] = 'nullable';
        }

        // กฎการตรวจสอบแบบมีเงื่อนไขสำหรับโปรโมชั่นต่อเนื่อง (recurring promotion)
        if ($request->boolean('is_recurring_promotion')) {
            $rules['recurring_promotion_days'] = 'required|integer|min:1';
            $rules['recurring_bonus_percentage'] = 'required|numeric|min:0|max:100';

            // *** NEW: Validation สำหรับ recurring_turnover fields ***
            // ใช้ required_without ถ้าต้องการให้ต้องกรอกอย่างใดอย่างหนึ่ง (ไม่พร้อมกัน)
            $rules['recurring_turnover_percentage'] = 'required_without:recurring_turnover|nullable|numeric|min:0';

        } else {
            $rules['recurring_promotion_days'] = 'nullable';
            $rules['recurring_bonus_percentage'] = 'nullable';
            // *** NEW: ทำให้ช่อง recurring turnover เป็น nullable หากไม่ใช่โปรโมชั่นต่อเนื่อง ***
            $rules['recurring_turnover'] = 'nullable';
            $rules['recurring_turnover_percentage'] = 'nullable';
        }

        // รันการตรวจสอบ
        $request->validate($rules);


        // 3. กำหนดข้อมูลจาก request ให้กับโมเดล
        $pro->name = $request->name;
        $pro->deposit = $request->deposit;
        $pro->description = $request->description;
        $pro->active = true; // สมมติว่ายังคง active หลังจากอัปเดต หากไม่มีการจัดการอย่างชัดเจน

        // 4. จัดการค่า Bonus, Turnover, Withdraw Limit (บาท/เท่า หรือ เปอร์เซ็นต์)
        $pro->is_percentage_based = $request->boolean('is_percentage_based'); // กำหนดค่านี้ก่อน

        if ($pro->is_percentage_based) {
            $pro->bonus = 0.0;
            $pro->bonus_percentage = $request->bonus_percentage;

            $pro->turnover = 0.0;
            $pro->turnover_percentage = $request->turnover_percentage;

            $pro->withdraw_limit = 0.0;
            $pro->withdraw_limit_percentage = $request->withdraw_limit_percentage;
        } else {
            $pro->bonus = $request->bonus;
            $pro->bonus_percentage = null;

            $pro->turnover = $request->turnover;
            $pro->turnover_percentage = null;

            $pro->withdraw_limit = $request->withdraw_limit;
            $pro->withdraw_limit_percentage = null;
        }

        // 5. จัดการ Checkbox fields (boolean)
        $pro->enable = $request->boolean('enable');
        $pro->is_newuser = $request->boolean('is_newuser');
        // $pro->is_percentage_based ถูกกำหนดไปแล้วด้านบน
        $pro->is_recurring_promotion = $request->boolean('is_recurring_promotion');
        $pro->is_first_deposit_bonus = $request->boolean('is_first_deposit_bonus');

        // 6. จัดการ recurring_promotion_days, recurring_bonus_percentage, และ NEW recurring_turnover fields
        if ($pro->is_recurring_promotion) { // ใช้ $pro->is_recurring_promotion หลังจากกำหนดค่าแล้ว
            $pro->recurring_promotion_days = $request->recurring_promotion_days;
            $pro->recurring_bonus_percentage = $request->recurring_bonus_percentage;
            // *** NEW: กำหนดค่า recurring turnover ***
            // $pro->recurring_turnover = $request->recurring_turnover;
            $pro->recurring_turnover_percentage = $request->recurring_turnover_percentage;
        } else {
            $pro->recurring_promotion_days = null;
            $pro->recurring_bonus_percentage = null;
            // *** NEW: ตั้งค่า recurring turnover เป็น null หากไม่ใช่โปรโมชั่นต่อเนื่อง ***
            // $pro->recurring_turnover = null;
            $pro->recurring_turnover_percentage = null;
        }

        // 7. จัดการ applicable_games
        $pro->applicable_games = $request->input('applicable_games');

        // 8. จัดการรูปภาพ
        if ($request->hasFile('image')) {
            // ลบรูปเก่าถ้ามี
            // ควรตรวจสอบว่า $pro->image มีค่าและไฟล์นั้นมีอยู่จริงก่อนลบ
            if ($pro->image && file_exists(public_path(trim($pro->image, '/')))) { // ใช้ trim('/') เพื่อให้ได้ path ที่ถูกต้อง
                unlink(public_path(trim($pro->image, '/')));
            }

            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('_image'), $fileName);
            $pro->image = '/_image/' . $fileName;
        }
        // หากไม่มีการอัปโหลดรูปใหม่ และค่าเดิมถูกลบไปแล้ว แต่ต้องการให้มีค่า default
        // อาจจะต้องพิจารณาเพิ่มเติมว่าต้องการเก็บรูปเดิมไว้ หรือต้องการให้เป็น default
        // ในเคสนี้ ถ้าไม่มีการอัปโหลดใหม่และ $pro->image เป็นค่าเดิม ก็จะไม่มีการเปลี่ยนแปลง
        // ถ้าต้องการบังคับให้มีรูปภาพเสมอ (แม้จะไม่มีการอัปโหลดใหม่) ก็ต้องเพิ่มเงื่อนไข
        // เช่น else if (empty($pro->image)) { $pro->image = '/_image/default.png'; }

        // 9. กำหนดค่าเริ่มต้นสำหรับคอลัมน์อื่นๆ ที่เป็น NOT NULL ใน DB และไม่มีในฟอร์ม (ถ้าจำเป็น)
        // ตรวจสอบจาก DB schema ของคุณว่าคอลัมน์เหล่านี้มีค่า default หรือไม่
        // และถ้าไม่มีในฟอร์มจริงๆ คุณต้องการให้มันเป็นอะไรเมื่อมีการ update
        // ผมจะสมมติว่าถ้าไม่มีใน request ก็ใช้ค่าเดิมของ model หรือค่า default
        if (is_null($pro->store_id)) {
            $pro->store_id = 1; // Example default if not handled elsewhere
        }
        if (is_null($pro->withdraw_percent)) {
            $pro->withdraw_percent = 0; // Example default if not handled elsewhere
        }

        try {
            // 10. บันทึกการเปลี่ยนแปลง
            $pro->save();
            return redirect()->route('promotion.index')->with('status', '200');
        } catch (\Exception $e) {
            Log::error("Error updating promotion: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update promotion. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $pro = Promotion::find($request->id);
        $pro->active = 0;
        $pro->save();
        return redirect()->route('promotion.index')->with('status', '200');
    }
}
