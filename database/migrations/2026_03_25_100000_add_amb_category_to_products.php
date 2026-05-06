<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amb_products', function (Blueprint $table) {
            if (!Schema::hasColumn('amb_products', 'amb_category_id')) {
                $table->foreignId('amb_category_id')
                    ->nullable()
                    ->after('product_name')
                    ->constrained('amb_categories')
                    ->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('amb_products', function (Blueprint $table) {
            if (Schema::hasColumn('amb_products', 'amb_category_id')) {
                $table->dropForeign(['amb_category_id']);
                $table->dropColumn('amb_category_id');
            }
        });
    }
};
