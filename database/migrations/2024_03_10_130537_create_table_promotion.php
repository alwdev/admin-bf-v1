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
        if (!Schema::hasTable('promotion')) {
            Schema::create('promotion', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('bonus')->default(0);
                $table->integer('turnover')->default(0);
                $table->boolean('enable')->default(1);
                $table->boolean('active')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion');
    }
};
