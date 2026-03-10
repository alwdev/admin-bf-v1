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
        if (!Schema::hasTable('sbo_providers')) {
            Schema::create('sbo_providers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('gpid')->nullable();
                $table->string('name');
                $table->string('type')->nullable();
                $table->unsignedBigInteger('lobby_game_id')->nullable();
                $table->string('img')->nullable();
                $table->boolean('supports_game_id_login')->default(false);
                $table->string('devices')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->index(['name', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sbo_providers');
    }
};

