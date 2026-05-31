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
        Schema::table('transfer', function (Blueprint $table) {
            $table->boolean('is_turnover_cleared')->default(0)->after('turnover_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer', function (Blueprint $table) {
            $table->dropColumn('is_turnover_cleared');
        });
    }
};
