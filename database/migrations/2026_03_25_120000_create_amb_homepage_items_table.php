<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('amb_homepage_items')) {
            Schema::create('amb_homepage_items', function (Blueprint $table) {
                $table->id();
                // ตรงกับ sbo_homepage_items: hot | slot | livecasino
                $table->string('category', 32);
                $table->unsignedTinyInteger('position');
                $table->foreignId('amb_game_id')->constrained('amb_games')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['category', 'position']);
                $table->unique(['category', 'amb_game_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('amb_homepage_items');
    }
};
