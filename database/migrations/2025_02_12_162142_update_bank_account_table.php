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
        // Schema::table('bank_account', function (Blueprint $table) {
        //     $table->string('prompay_no')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bank_account')) {
            Schema::table('bank_account', function (Blueprint $table) {
                if (Schema::hasColumn('bank_account', 'prompay_no')) {
                    $table->dropColumn('prompay_no');
                }
            });
        }
    }
};
