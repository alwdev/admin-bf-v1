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
        if (!Schema::hasColumn('transfer', 'turnover_on')) {
            Schema::table('transfer', function (Blueprint $table) {
                $table->boolean('turnover_on')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transfer', 'turnover_on')) {
            Schema::table('transfer', function (Blueprint $table) {
                $table->dropColumn('turnover_on');
            });
        }
    }
};
