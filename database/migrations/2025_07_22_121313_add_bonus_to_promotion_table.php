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
                if (!Schema::hasColumn('promotion', 'bonus_percentage')) {
                    $table->decimal('bonus_percentage', 18, 2)->nullable()->after('bonus');
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
                if (Schema::hasColumn('promotion', 'bonus_percentage')) {
                    $table->dropColumn('bonus_percentage');
                }
            });
        }
    }
};
