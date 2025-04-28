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
        //
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('category', ['บอล', 'หวย', 'ดูหนังออนไลน์', '18+','การพนัน','ข่าวในประเทศ'])->nullable();
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
