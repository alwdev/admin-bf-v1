<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('promotion', function (Blueprint $table) {
            // เพิ่มคอลัมน์สำหรับ Turnover ของโปรโมชั่นต่อเนื่อง
            // ใช้ float เพื่อรองรับค่าเปอร์เซ็นต์ หรือจำนวนเท่า
            // ค่า default ควรเป็น 0 หรือ null ตามที่คุณต้องการ
            $table->float('recurring_turnover')->default(0.0)->after('recurring_bonus_percentage')->comment('ค่า Turnover สำหรับโปรโมชั่นต่อเนื่อง (จำนวนเท่า หรือ ค่าตายตัว)');
            $table->float('recurring_turnover_percentage')->nullable()->after('recurring_turnover')->comment('ค่า Turnover Percentage สำหรับโปรโมชั่นต่อเนื่อง (กรณีเป็นเปอร์เซ็นต์)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotion', function (Blueprint $table) {
            //
        });
    }
};
