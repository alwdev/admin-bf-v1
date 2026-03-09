<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
    {
        if (!Schema::hasTable('hashtags')) {
            Schema::create('hashtags', function (Blueprint $table) {
                $table->id();  // คอลัมน์ id แบบ BIGINT
                $table->string('hashtag', 255);  // คอลัมน์ hashtag ขนาด 255 ตัวอักษร
                $table->string('link', 255);  // คอลัมน์ link ขนาด 255 ตัวอักษร
                $table->timestamps();  // คอลัมน์ created_at และ updated_at
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtags');
    }
};
