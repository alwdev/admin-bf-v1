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
                if (!Schema::hasColumn('setting', 'mission_deposit_goal')) {
                    $table->decimal('mission_deposit_goal',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'mission_deposit_point')) {
                    $table->decimal('mission_deposit_point',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'mission_deposit_credit')) {
                    $table->decimal('mission_deposit_credit',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'is_enable_mission_deposit')) {
                    $table->boolean('is_enable_mission_deposit')->default(1);
                }

                if (!Schema::hasColumn('setting', 'mission_play_goal')) {
                    $table->decimal('mission_play_goal',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'mission_play_point')) {
                    $table->decimal('mission_play_point',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'mission_play_credit')) {
                    $table->decimal('mission_play_credit',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'is_enable_mission_play')) {
                    $table->boolean('is_enable_mission_play')->default(1);
                }

                if (!Schema::hasColumn('setting', 'mission_win_goal')) {
                    $table->decimal('mission_win_goal',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'mission_win_point')) {
                    $table->decimal('mission_win_point',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'mission_win_credit')) {
                    $table->decimal('mission_win_credit',16,2)->default(0);
                }
                if (!Schema::hasColumn('setting', 'is_enable_mission_win')) {
                    $table->boolean('is_enable_mission_win')->default(1);
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
