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
        Schema::create('wheelspin', function (Blueprint $table) {
            $table->id();
            $table->decimal('ticket_condition',18,2)->default(0);
            $table->integer('limit_per_day')->default(0);
            $table->integer('limit_person')->default(0);
            $table->boolean('enable')->default(0);
            $table->decimal('limit_withdraw',18,2)->default(0);
            $table->decimal('trunover',18,2)->default(0);
            $table->decimal('win_1',18,2)->default(0);
            $table->string('win_1_reward')->nullable();
            $table->string('win_1_rate')->nullable();
            $table->decimal('win_2',18,2)->default(0);
            $table->string('win_2_reward')->nullable();
            $table->string('win_2_rate')->nullable();
            $table->decimal('win_3',18,2)->default(0);
            $table->string('win_3_reward')->nullable();
            $table->string('win_3_rate')->nullable();
            $table->decimal('win_4',18,2)->default(0);
            $table->string('win_4_reward')->nullable();
            $table->string('win_4_rate')->nullable();
            $table->decimal('win_5',18,2)->default(0);
            $table->string('win_5_reward')->nullable();
            $table->string('win_5_rate')->nullable();
            $table->decimal('win_6',18,2)->default(0);
            $table->string('win_6_reward')->nullable();
            $table->string('win_6_rate')->nullable();
            $table->decimal('win_7',18,2)->default(0);
            $table->string('win_7_reward')->nullable();
            $table->string('win_7_rate')->nullable();
            $table->decimal('win_8',18,2)->default(0);
            $table->string('win_8_reward')->nullable();
            $table->string('win_8_rate')->nullable();
            $table->decimal('win_9',18,2)->default(0);
            $table->string('win_9_reward')->nullable();
            $table->string('win_9_rate')->nullable();
            $table->decimal('win_10',18,2)->default(0);
            $table->string('win_10_reward')->nullable();
            $table->string('win_10_rate')->nullable();
            $table->decimal('win_11',18,2)->default(0);
            $table->string('win_11_reward')->nullable();
            $table->string('win_11_rate')->nullable();
            $table->decimal('win_12',18,2)->default(0);
            $table->string('win_12_reward')->nullable();
            $table->string('win_12_rate')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

          // Insert some stuff
        DB::table('wheelspin')->insert(
            array(
                'enable' => '1',
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheelspin');
    }
};
