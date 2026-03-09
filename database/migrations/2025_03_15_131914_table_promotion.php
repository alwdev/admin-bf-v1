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
        if (!Schema::hasColumn('promotion', 'description')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
        if (!Schema::hasColumn('promotion', 'image')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->string('image')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('promotion', 'image')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
        if (Schema::hasColumn('promotion', 'description')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
