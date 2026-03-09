<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            // Fix: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: '0000-00-00'
            // MySQL 5.7+ / 8.0+ strict mode does not allow '0000-00-00'
            try {
                DB::statement("UPDATE users SET email_verified_at = NULL WHERE email_verified_at = '0000-00-00 00:00:00'");
            } catch (\Exception $e) {
                // Ignore if the column or table doesn't exist yet
            }

            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'lotto_login_token')) {
                    $table->text('lotto_login_token')->nullable();
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
