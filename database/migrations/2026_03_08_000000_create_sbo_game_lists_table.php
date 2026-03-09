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
        if (!Schema::hasTable('sbo_game_lists')) {
            Schema::create('sbo_game_lists', function (Blueprint $table) {
                $table->id();
                $table->string('gpid')->nullable();
                $table->string('provider_name');
                $table->string('provider_type')->nullable();
                $table->string('game_id')->nullable();
                $table->string('game_name');
                $table->string('game_code')->nullable();
                $table->string('img')->nullable();
                $table->boolean('active')->default(1);
                $table->text('payload')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sbo_game_lists');
    }
};
