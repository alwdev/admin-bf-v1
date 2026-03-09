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
            if (!Schema::hasColumn('promotion', 'withdraw_limit')) {
                Schema::table('promotion', function (Blueprint $table) {
                    $table->decimal('withdraw_limit', 18, 2)->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('promotion', 'withdraw_limit')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->dropColumn('withdraw_limit');
            });
        }
    }
};
