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
        if (!Schema::hasTable('wheel_history')) {
            Schema::create('wheel_history', function (Blueprint $table) {
                $table->id();
                $table->string('member_id');
                $table->string('win_text')->nullable();
                $table->string('turnover')->nullable();
                $table->decimal('win',18,2)->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('transfer')) {
            Schema::table('transfer', function (Blueprint $table) {
                if (!Schema::hasColumn('transfer', 'turnover')) {
                    $table->decimal('turnover',18,2)->default(0);
                }
                if (!Schema::hasColumn('transfer', 'turnover_balance')) {
                    $table->decimal('turnover_balance',18,2)->default(0);
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
