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
        Schema::table('setting', function (Blueprint $table) {
            $table->decimal('min_deposit',16,2)->default(0);
            $table->decimal('min_withdraw',16,2)->default(0);
            $table->decimal('auto_min_withdraw',16,2)->default(0);
            $table->decimal('cashback_percent',16,2)->default(0);
            $table->decimal('cashback_turnover',16,2)->default(0);
            $table->decimal('cashback_min_withdraw',16,2)->default(0);

            $table->boolean('is_enable_min_deposit')->default(1);
            $table->boolean('is_enable_cashback')->default(1);
            $table->boolean('is_enable_auto_withdraw')->default(1);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};