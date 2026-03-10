<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sbo_providers') && Schema::hasColumn('sbo_providers', 'gpid')) {
            try {
                DB::statement('ALTER TABLE `sbo_providers` MODIFY `gpid` BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE `sbo_providers` MODIFY `gpid` BIGINT NULL');
                } catch (\Throwable $e2) {
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sbo_providers') && Schema::hasColumn('sbo_providers', 'gpid')) {
            try {
                DB::statement('ALTER TABLE `sbo_providers` MODIFY `gpid` BIGINT UNSIGNED NOT NULL DEFAULT 0');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE `sbo_providers` MODIFY `gpid` BIGINT NOT NULL DEFAULT 0');
                } catch (\Throwable $e2) {
                }
            }
        }
    }
};

