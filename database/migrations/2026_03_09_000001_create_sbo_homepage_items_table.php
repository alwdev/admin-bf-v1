<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbo_homepage_items', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // hot | slot | livecasino
            $table->unsignedTinyInteger('position'); // 1..10
            $table->unsignedBigInteger('game_list_id');
            $table->timestamps();

            $table->foreign('game_list_id')->references('id')->on('sbo_game_lists')->onDelete('cascade');
            $table->unique(['category', 'position']);
            $table->unique(['category', 'game_list_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbo_homepage_items');
    }
};

