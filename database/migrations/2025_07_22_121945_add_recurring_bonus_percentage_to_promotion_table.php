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
            // เพิ่มคอลัมน์ recurring_bonus_percentage เป็น decimal(5,2) สำหรับเปอร์เซ็นต์
            // 5 หลักรวมทศนิยม 2 ตำแหน่ง (เช่น 100.00)
            $table->decimal('recurring_bonus_percentage', 5, 2)->nullable()->after('recurring_promotion_days')->comment('โบนัสเปอร์เซ็นต์สำหรับโปรโมชั่นต่อเนื่อง');
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
