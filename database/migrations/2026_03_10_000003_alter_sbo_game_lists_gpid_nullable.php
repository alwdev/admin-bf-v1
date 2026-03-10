<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sbo_game_lists') && Schema::hasColumn('sbo_game_lists', 'gpid')) {
            try {
                DB::statement('ALTER TABLE `sbo_game_lists` MODIFY `gpid` BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE `sbo_game_lists` MODIFY `gpid` BIGINT NULL');
                } catch (\Throwable $e2) {
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sbo_game_lists') && Schema::hasColumn('sbo_game_lists', 'gpid')) {
            try {
                DB::statement('ALTER TABLE `sbo_game_lists` MODIFY `gpid` BIGINT UNSIGNED NOT NULL DEFAULT 0');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE `sbo_game_lists` MODIFY `gpid` BIGINT NOT NULL DEFAULT 0');
                } catch (\Throwable $e2) {
                }
            }
        }
    }
};

