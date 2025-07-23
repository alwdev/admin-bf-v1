<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // เพิ่มบรรทัดนี้

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promotion', function (Blueprint $table) {
            // หาก applicable_games ยังคงเป็น VARCHAR หรือ TEXT
            // ให้เปลี่ยนเป็น JSON โดยจัดการข้อมูลเก่าถ้ามี
            if (DB::getDriverName() === 'mysql' && version_compare(DB::connection()->getPdo()->query('select version()')->fetchColumn(), '5.7.8', '>=')) {
                // MySQL 5.7.8+ รองรับ JSON type
                $table->longText('applicable_games')->change();
            } else {
                // สำหรับ MySQL รุ่นเก่า หรือ database อื่นๆ ที่ไม่มี JSON type โดยตรง
                $table->text('applicable_games')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotion', function (Blueprint $table) {
            // กำหนดการย้อนกลับไปเป็น TEXT หรือ VARCHAR หากจำเป็น
            // พิจารณาว่าข้อมูลใน applicable_games จะเสียหายหรือไม่
            $table->text('applicable_games')->change(); // หรือ $table->string('applicable_games', 255)->change();
        });
    }
};
