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
        if (!Schema::hasTable('ranking')) {
            Schema::create('ranking', function (Blueprint $table) {
                $table->id();
                $table->string('credit')->nullable();
                $table->string('diamond')->nullable();
                $table->string('exp')->nullable();
                $table->text('image')->nullable();
                $table->integer('rank')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking');
    }
};
