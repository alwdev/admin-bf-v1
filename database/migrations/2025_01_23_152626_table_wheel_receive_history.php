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
        Schema::create('wheel_history', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->string('win_text')->nullable();
            $table->string('turnover')->nullable();
            $table->decimal('win',18,2)->default(0);
            $table->timestamps();
        });

        Schema::table('transfer', function (Blueprint $table) {
            $table->decimal('turnover',18,2)->default(0);
            $table->decimal('turnover_balance',18,2)->default(0);
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
