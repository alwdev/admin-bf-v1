<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('amb_categories')) {
            Schema::create('amb_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('name_th')->nullable();
                $table->unsignedInteger('order_no')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('amb_products')) {
            Schema::create('amb_products', function (Blueprint $table) {
                $table->id();
                $table->string('product_code')->unique();
                $table->string('product_name');
                $table->string('img')->nullable();
                $table->unsignedInteger('order_no')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('amb_games')) {
            Schema::create('amb_games', function (Blueprint $table) {
                $table->id();
                $table->foreignId('amb_product_id')->constrained('amb_products')->restrictOnDelete();
                $table->foreignId('amb_category_id')->constrained('amb_categories')->restrictOnDelete();
                $table->string('game_code');
                $table->string('game_name');
                $table->string('game_type')->nullable();
                $table->string('img')->nullable();
                $table->unsignedInteger('rank')->default(0);
                $table->string('provider_code')->nullable();
                $table->json('locale')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->unique(['amb_product_id', 'game_code']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('amb_games');
        Schema::dropIfExists('amb_products');
        Schema::dropIfExists('amb_categories');
    }
};
