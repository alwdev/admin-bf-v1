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
        Schema::create('level_member', function (Blueprint $table) {
            $table->id();
            $table->string('level_name')->nullable();
            $table->string('level_min_point')->nullable();
            $table->string('level_max_point')->nullable();
            $table->text('image')->nullable();
            $table->integer('rank')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_member');
    }
};