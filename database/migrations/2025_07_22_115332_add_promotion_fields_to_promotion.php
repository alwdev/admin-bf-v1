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
                if (!Schema::hasColumn('promotion', 'is_percentage_based')) {
                    $table->boolean('is_percentage_based')->default(false);
                }

                if (!Schema::hasColumn('promotion', 'turnover_percentage')) {
                    $table->decimal('turnover_percentage', 18, 2)->nullable()->after('turnover')->comment('เปอร์เซ็นต์ของยอดเทิร์นโอเวอร์');
                }

                if (!Schema::hasColumn('promotion', 'withdraw_limit_percentage')) {
                    $table->decimal('withdraw_limit_percentage', 18, 2)->nullable()->after('withdraw_limit')->comment('เปอร์เซ็นต์ของยอดถอนสูงสุด');
                }

                if (!Schema::hasColumn('promotion', 'is_recurring_promotion')) {
                    $table->boolean('is_recurring_promotion')->default(false)->after('withdraw_limit_percentage')->comment('ระบุว่าเป็นโปรโมชั่นที่เกิดซ้ำ/ต่อเนื่องหรือไม่');
                }

                if (!Schema::hasColumn('promotion', 'recurring_promotion_days')) {
                    $table->integer('recurring_promotion_days')->nullable()->after('is_recurring_promotion')->comment('จำนวนวันของโปรโมชั่นที่เกิดซ้ำ (ถ้ามี)');
                }

                if (!Schema::hasColumn('promotion', 'applicable_games')) {
                    $table->longText('applicable_games')->nullable()->after('description')->comment('ระบุเกมที่สามารถใช้โปรโมชั่นนี้ได้ (เช่น ทั้งหมด, สล็อต, คาสิโนสด)');
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
                if (Schema::hasColumn('promotion', 'is_percentage_based')) {
                    $table->dropColumn('is_percentage_based');
                }
                if (Schema::hasColumn('promotion', 'turnover_percentage')) {
                    $table->dropColumn('turnover_percentage');
                }
                if (Schema::hasColumn('promotion', 'withdraw_limit_percentage')) {
                    $table->dropColumn('withdraw_limit_percentage');
                }
                if (Schema::hasColumn('promotion', 'is_recurring_promotion')) {
                    $table->dropColumn('is_recurring_promotion');
                }
                if (Schema::hasColumn('promotion', 'recurring_promotion_days')) {
                    $table->dropColumn('recurring_promotion_days');
                }
                if (Schema::hasColumn('promotion', 'applicable_games')) {
                    $table->dropColumn('applicable_games');
                }
            });
        }
    }
};
