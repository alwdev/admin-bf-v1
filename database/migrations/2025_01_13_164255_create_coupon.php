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
        if (!Schema::hasTable('coupon')) {
            Schema::create('coupon', function (Blueprint $table) {
                $table->id();
                $table->string('coupon');
                $table->integer('max')->default(0);
                $table->integer('used')->default(0);
                $table->decimal('amount',16,2)->default(0);
                $table->string('date_start')->nullable();
                $table->string('date_end')->nullable();
                $table->boolean('active')->default(1);
                $table->boolean('enable')->default(1);
                $table->string('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon');
    }
};
