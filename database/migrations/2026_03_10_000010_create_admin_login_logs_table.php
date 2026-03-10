<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_login_logs')) {
            Schema::create('admin_login_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('guard', 50)->nullable();
                $table->string('action', 20);
                $table->boolean('succeeded')->default(false);
                $table->string('identifier')->nullable();
                $table->string('failure_reason', 100)->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('session_id', 100)->nullable();
                $table->boolean('remember')->default(false);
                $table->string('method', 10)->nullable();
                $table->string('path', 2048)->nullable();
                $table->timestamp('logged_in_at')->nullable();
                $table->timestamp('logged_out_at')->nullable();
                $table->unsignedTinyInteger('user_level')->nullable();
                $table->json('user_permissions')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['ip', 'created_at']);
                $table->index(['action', 'created_at']);
            });

            if (Schema::hasTable('users')) {
                Schema::table('admin_login_logs', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_login_logs');
    }
};

