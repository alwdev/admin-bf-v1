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
        if (Schema::hasTable('affiliate')) {
            Schema::table('affiliate', function (Blueprint $table) {
                if (Schema::hasColumn('affiliate', 'af_receive_percent_winlose_1')) {
                    $table->text('af_receive_percent_winlose_1')->change();
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