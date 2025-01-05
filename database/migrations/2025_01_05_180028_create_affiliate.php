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
        Schema::create('affiliate', function (Blueprint $table) {
            $table->id();
            $table->decimal('af_min_deposit',16,2)->default(0);
            $table->decimal('af_deposit_receive_lv_1',16,2)->default(0);
            $table->decimal('af_deposit_receive_lv_2',16,2)->default(0);
            $table->string('af_deposit_type')->nullable();
            $table->decimal('af_max_receive_deposit_percent',16,2)->default(0);
            $table->decimal('af_max_receive_deposit_baht',16,2)->default(0);
            $table->boolean('is_enable_af_deposit')->default(1);
            $table->boolean('is_enable_af_winlose')->default(1);
            $table->decimal('af_receive_percent_winlose_1',16,2)->default(0);
            $table->decimal('af_receive_percent_winlose_2',16,2)->default(0);
            $table->decimal('af_receive_percent_winlose_3',16,2)->default(0);
            $table->string('updated_by')->nullable();
            $table->string('updated_date')->nullable();
            $table->timestamps();
        });

        DB::table('affiliate')->insert([
            [
                'af_min_deposit' => 0, 
                'af_deposit_receive_lv_1' => 0, 
                'af_deposit_receive_lv_2' => 0, 
                'af_deposit_type' => 0, 
                'af_max_receive_deposit_percent' => 0, 
                'af_max_receive_deposit_baht' => 0, 
                'is_enable_af_deposit' => 1, 
                'is_enable_af_winlose' => 1, 
                'af_receive_percent_winlose_1' => 0, 
                'af_receive_percent_winlose_2' => 0, 
                'af_receive_percent_winlose_3' => 0, 
                'updated_by' => NULL, 
                'updated_date' => NULL, 
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate');
    }
};
