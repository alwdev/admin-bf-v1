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
        Schema::table('articles', function (Blueprint $table) {
            $table->text('tags')->nullable();  // เก็บ Tags เป็น String
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
