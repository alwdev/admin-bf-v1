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
        if (Schema::hasTable('promotion')) {
            if (!Schema::hasColumn('promotion', 'is_newuser')) {
                Schema::table('promotion', function (Blueprint $table) {
                    $table->boolean('is_newuser')->default(false);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('promotion', 'is_newuser')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->dropColumn('is_newuser');
            });
        }
    }
};
