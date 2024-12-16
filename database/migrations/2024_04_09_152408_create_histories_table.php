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
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->index('id');
            $table->string('request_id');
            $table->string('roundId');
            $table->string('username');
            $table->string('game');
            $table->string('provider');
            $table->string('amount');
            $table->string('winlose');
            $table->string('type');
            $table->timestamp('playtime');
            $table->string('balanceBefore')->default(0);
            $table->string('balanceAfter')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
