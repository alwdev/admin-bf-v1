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
        Schema::create('home_pages', function (Blueprint $table) { // ใช้ชื่อเป็นพหูพจน์ตาม convention ของ Laravel
            $table->id();

            // 1. เนื้อหาหลัก (ใช้ JSON เพื่อความยืดหยุ่นในการเก็บข้อมูลแบบมีโครงสร้าง)
            $table->longText('content')->nullable()->comment('Main content data in JSON format');

            // 2. Metadata/SEO fields
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable(); // หรืออาจใช้ text

            // 3. สถานะและการจัดการ
            $table->boolean('active')->default(true)->comment('Determines if this version/content block is currently active');
            $table->unsignedInteger('revision')->default(1)->comment('Content revision number'); // เพิ่ม revision เพื่อติดตามการเปลี่ยนแปลง

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_pages');
    }
};
