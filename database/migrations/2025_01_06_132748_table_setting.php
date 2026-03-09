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
        if (Schema::hasTable('setting')) {
            Schema::table('setting', function (Blueprint $table) {
                if (!Schema::hasColumn('setting', 'min_deposit')) {
                    $table->decimal('min_deposit',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'min_withdraw')) {
                    $table->decimal('min_withdraw',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'auto_min_withdraw')) {
                    $table->decimal('auto_min_withdraw',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'cashback_percent')) {
                    $table->decimal('cashback_percent',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'cashback_turnover')) {
                    $table->decimal('cashback_turnover',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'cashback_min_withdraw')) {
                    $table->decimal('cashback_min_withdraw',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'is_enable_min_deposit')) {
                    $table->boolean('is_enable_min_deposit')->default(1);
                }
                if (!Schema::hasColumn('setting', 'is_enable_cashback')) {
                    $table->boolean('is_enable_cashback')->default(1);
                }
                if (!Schema::hasColumn('setting', 'is_enable_auto_withdraw')) {
                    $table->boolean('is_enable_auto_withdraw')->default(1);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};