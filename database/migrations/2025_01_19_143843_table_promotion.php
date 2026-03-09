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
        if (!Schema::hasColumn('promotion', 'deposit')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->decimal('deposit',18,2)->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('promotion', 'deposit')) {
            Schema::table('promotion', function (Blueprint $table) {
                $table->dropColumn('deposit');
            });
        }
    }
};
