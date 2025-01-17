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
            $table->decimal('ticket_condition',16,2)->default(500);
            $table->integer('limit_per_day')->default(0);
            $table->integer('limit_person')->default(0);
            $table->boolean('enable')->default(1);
            $table->string('image')->nullable();
            $table->decimal('limit_withdraw',16,2)->default(0);
            $table->decimal('trunover',16,2)->default(0);
            $table->string('win_1_reward')->nullable();
            $table->decimal('win_1',16,2)->default(0);
            $table->decimal('win_1_rate',16,2)->default(0);
            $table->string('win_2_reward')->nullable();
            $table->decimal('win_2',16,2)->default(0);
            $table->decimal('win_2_rate',16,2)->default(0);
            $table->string('win_3_reward')->nullable();
            $table->decimal('win_3',16,2)->default(0);
            $table->decimal('win_3_rate',16,2)->default(0);
            $table->string('win_4_reward')->nullable();
            $table->decimal('win_4',16,2)->default(0);
            $table->decimal('win_4_rate',16,2)->default(0);
            $table->string('win_5_reward')->nullable();
            $table->decimal('win_5',16,2)->default(0);
            $table->decimal('win_5_rate',16,2)->default(0);
            $table->string('win_6_reward')->nullable();
            $table->decimal('win_6',16,2)->default(0);
            $table->decimal('win_6_rate',16,2)->default(0);
            $table->string('win_7_reward')->nullable();
            $table->decimal('win_7',16,2)->default(0);
            $table->decimal('win_7_rate',16,2)->default(0);
            $table->string('win_8_reward')->nullable();
            $table->decimal('win_8',16,2)->default(0);
            $table->decimal('win_8_rate',16,2)->default(0);
            $table->string('win_9_reward')->nullable();
            $table->decimal('win_9',16,2)->default(0);
            $table->decimal('win_9_rate',16,2)->default(0);
            $table->string('win_10_reward')->nullable();
            $table->decimal('win_10',16,2)->default(0);
            $table->decimal('win_10_rate',16,2)->default(0);
            $table->string('win_11_reward')->nullable();
            $table->decimal('win_11',16,2)->default(0);
            $table->decimal('win_11_rate',16,2)->default(0);
            $table->string('win_12_reward')->nullable();
            $table->decimal('win_12',16,2)->default(0);
            $table->decimal('win_12_rate',16,2)->default(0);
            $table->timestamps();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->integer('wheel_spin')->default(0);
        });

        Schema::create('wheelspin_history', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->string('win_reward');
            $table->timestamps();
        });

        DB::table('wheelspin')->insert([
            "enable" => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheelspin');
    }
};
