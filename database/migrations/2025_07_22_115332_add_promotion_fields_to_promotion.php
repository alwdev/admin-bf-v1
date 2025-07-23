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
            // is_use_percent (โปรโมชั่นนี้ใช้รูปการคำนวนเป็น %)
            // แนะนำเป็น is_percentage_based หรือ calculation_type
            // ถ้าเป็น boolean is_percentage_based ก็เหมาะสมครับ
            $table->boolean('is_percentage_based')->default(false);

            // turnover_percent
            // ถ้าเป็นเปอร์เซ็นต์ ควรใช้ decimal เพื่อความแม่นยำ
            $table->decimal('turnover_percentage', 5, 2)->nullable()->after('turnover')->comment('เปอร์เซ็นต์ของยอดเทิร์นโอเวอร์');

            // withdraw_limit_percent
            // ถ้าเป็นเปอร์เซ็นต์ ควรใช้ decimal
            $table->decimal('withdraw_limit_percentage', 5, 2)->nullable()->after('withdraw_limit')->comment('เปอร์เซ็นต์ของยอดถอนสูงสุด');

            // is_continuously_promotion (เป็นโปรโมชั่นต่อเนื่อง มีคำที่ตรงกว่านี้มั้ยสามารถแก้ให้ได้เลยนะ)
            // แนะนำเป็น is_recurring, is_evergreen, หรือ promotion_type (ถ้ามีหลายประเภท)
            // ถ้าเป็น boolean ผมแนะนำ is_recurring หรือ is_evergreen ครับ
            $table->boolean('is_recurring_promotion')->default(false)->after('withdraw_limit_percentage')->comment('ระบุว่าเป็นโปรโมชั่นที่เกิดซ้ำ/ต่อเนื่องหรือไม่');

            // continuously_promotion_days
            $table->integer('recurring_promotion_days')->nullable()->after('is_recurring_promotion')->comment('จำนวนวันของโปรโมชั่นที่เกิดซ้ำ (ถ้ามี)');

            // all_games (ใช้ในหน้าเกมไหนได้บ้าง อยากให้มีค่าเริ่มต้นเป็น ทั้งหมด และมีเกมค่าอื่นคือ สล็อต คาสิโนสด ยิงปลา เกมส์ไพ่ หวย กีฬา)
            // แนะนำให้เก็บเป็น JSON หรือใช้ตาราง pivot ถ้ามีหลายเกมที่เลือกได้ (Many-to-Many)
            // ในที่นี้จะใช้ JSON สำหรับตัวเลือกง่ายๆ
            $table->longText('applicable_games')->nullable()->after('description')->comment('ระบุเกมที่สามารถใช้โปรโมชั่นนี้ได้ (เช่น ทั้งหมด, สล็อต, คาสิโนสด)');
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
