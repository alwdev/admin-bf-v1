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
        // Schema::create('partner_commissions', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('partner_id');
        //     $table->string('amount');
        //     $table->string('payment_type');
        //     $table->string('payment_status')->nullable()->nullable();
        //     $table->string('transaction_id')->nullable();
        //     $table->string('note')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_commissions');
    }
};
