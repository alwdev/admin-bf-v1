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
        if (Schema::hasTable('promotion')) {
            Schema::table('promotion', function (Blueprint $table) {
                if (!Schema::hasColumn('promotion', 'is_first_deposit_bonus')) {
                    $table->boolean('is_first_deposit_bonus')->default(false)->after('is_newuser')->comment('ระบุว่าเป็นโปรโมชั่นสำหรับการฝากเงินครั้งแรกหรือไม่');
                }
                if (!Schema::hasColumn('promotion', 'recurring_promotion_bonus')) {
                    $table->decimal('recurring_promotion_bonus', 18, 2)->nullable()->after('recurring_promotion_days')->comment('โบนัสสำหรับโปรโมชั่นต่อเนื่อง (บาท)');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('promotion')) {
            Schema::table('promotion', function (Blueprint $table) {
                if (Schema::hasColumn('promotion', 'is_first_deposit_bonus')) {
                    $table->dropColumn('is_first_deposit_bonus');
                }
                if (Schema::hasColumn('promotion', 'recurring_promotion_bonus')) {
                    $table->dropColumn('recurring_promotion_bonus');
                }
            });
        }
    }
};
