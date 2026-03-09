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
            if (!Schema::hasColumn('member_edit_balance', 'type')) {
                Schema::table('member_edit_balance', function (Blueprint $table) {
                    $table->string('type')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('member_edit_balance', 'type')) {
            Schema::table('member_edit_balance', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
