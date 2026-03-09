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
        if (!Schema::hasTable('wrong_deposit')) {
            Schema::create('wrong_deposit', function (Blueprint $table) {
                $table->id();
                $table->foreignId('member_id');
                $table->decimal('amount',16,2)->default(0);
                $table->string('image')->nullable();
                $table->string('note')->nullable();
                $table->string('username')->nullable();
                $table->string('bank_from_number')->nullable();
                $table->string('bank_to_number')->nullable();
                $table->string('bank_from_name')->nullable();
                $table->string('bank_to_name')->nullable();
                $table->string('bank_from_account_name')->nullable();
                $table->string('bank_to_account_name')->nullable();
                $table->string('date')->nullable();
                $table->string('status_code')->nullable();
                $table->string('status')->nullable();
                $table->foreignId('user_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wrong_deposit');
    }
};
