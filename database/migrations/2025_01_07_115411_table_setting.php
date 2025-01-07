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
        //
        Schema::table('setting', function (Blueprint $table) {
            $table->decimal('point',16,2)->default(0);
            $table->decimal('turnover_point',16,2)->default(0);
            $table->boolean('is_enable_point')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
