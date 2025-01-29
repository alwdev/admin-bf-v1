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
        Schema::create('partner', function (Blueprint $table) {
            $table->id();
            $table->string('slug_name');
            $table->string('url');
            $table->string('contact_name');
            $table->string('contact_phonenumber');
            $table->string('contact_email')->nullable();
            $table->string('note')->nullable();
            $table->decimal('rate',16,2)->default(0);
            $table->decimal('total_profit',16,2)->default(0);
            $table->decimal('total_profit_rate',16,2)->default(0);
            $table->text('members')->nullable();
            $table->boolean('enable')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner');
    }
};
