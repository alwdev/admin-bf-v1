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
        Schema::table('setting', function (Blueprint $table) {
            if (!Schema::hasColumn('setting', 'point')) {
                $table->decimal('point',16,2)->default(0);
            }
            if (!Schema::hasColumn('setting', 'turnover_point')) {
                $table->decimal('turnover_point',16,2)->default(0);
            }
            if (!Schema::hasColumn('setting', 'is_enable_point')) {
                $table->boolean('is_enable_point')->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ลบคอลัมน์เพื่อความปลอดภัย
    }
};
