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
        if (Schema::hasTable('member_edit_balance')) {
            Schema::table('member_edit_balance', function (Blueprint $table) {
                if (!Schema::hasColumn('member_edit_balance', 'amount')) {
                    $table->decimal('amount',18,2)->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
