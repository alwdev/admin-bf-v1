<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_article_hashtag_table.php

    public function up()
    {
        if (!Schema::hasTable('article_hashtag')) {
            Schema::create('article_hashtag', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('article_id');
                $table->unsignedBigInteger('hashtag_id');
                $table->timestamps();

                // เช็คตารางอ้างอิงก่อนสร้าง Foreign Key เพื่อข้าม Error
                if (Schema::hasTable('articles')) {
                    $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
                }

                if (Schema::hasTable('hashtags')) {
                    $table->foreign('hashtag_id')->references('id')->on('hashtags')->onDelete('cascade');
                }
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_hashtag');
    }
};
