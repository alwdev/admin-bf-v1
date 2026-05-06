<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

return new class extends Migration
{
    public function up(): void
    {
        // #region agent log
        $debugLog = static function (string $hypothesisId, string $message, array $data = []): void {
            try {
                file_put_contents(
                    base_path('debug-d5deb8.log'),
                    json_encode([
                        'sessionId' => 'd5deb8',
                        'runId' => 'pre-fix',
                        'hypothesisId' => $hypothesisId,
                        'location' => 'database/migrations/2026_03_25_000000_create_amb_tables.php:up',
                        'message' => $message,
                        'data' => $data,
                        'timestamp' => (int) round(microtime(true) * 1000),
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                    FILE_APPEND
                );
            } catch (Throwable $e) {
                // Never break migration because debug logging failed.
            }
        };
        // #endregion

        // #region agent log
        $debugLog('H1', 'amb migration started', [
            'driver' => DB::connection()->getDriverName(),
            'version' => optional(DB::selectOne('select version() as v'))->v,
        ]);
        // #endregion

        // #region agent log
        $debugLog('H4', 'table existence before migration', [
            'amb_categories' => Schema::hasTable('amb_categories'),
            'amb_products' => Schema::hasTable('amb_products'),
            'amb_games' => Schema::hasTable('amb_games'),
        ]);
        // #endregion

        if (!Schema::hasTable('amb_categories')) {
            Schema::create('amb_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code', 191)->unique();
                $table->string('name');
                $table->string('name_th')->nullable();
                $table->unsignedInteger('order_no')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('amb_products')) {
            Schema::create('amb_products', function (Blueprint $table) {
                $table->id();
                $table->string('product_code', 191)->unique();
                $table->string('product_name');
                $table->string('img')->nullable();
                $table->unsignedInteger('order_no')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('amb_games')) {
            // #region agent log
            $debugLog('H2', 'creating amb_games with json column', [
                'game_code_length' => 150,
                'has_locale_json' => false,
                'locale_column_type' => 'longText',
                'has_rank_column' => true,
            ]);
            // #endregion
            try {
                Schema::create('amb_games', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('amb_product_id')->constrained('amb_products')->restrictOnDelete();
                    $table->foreignId('amb_category_id')->constrained('amb_categories')->restrictOnDelete();
                    $table->string('game_code', 150);
                    $table->string('game_name');
                    $table->string('game_type')->nullable();
                    $table->string('img')->nullable();
                    $table->unsignedInteger('rank')->default(0);
                    $table->string('provider_code')->nullable();
                    // MariaDB on some production servers does not support JSON column syntax here.
                    $table->longText('locale')->nullable();
                    $table->boolean('active')->default(true);
                    $table->timestamps();

                    $table->unique(['amb_product_id', 'game_code']);
                });
            } catch (Throwable $e) {
                // #region agent log
                $debugLog('H5', 'amb_games create failed', [
                    'error' => $e->getMessage(),
                    'amb_products_exists' => Schema::hasTable('amb_products'),
                    'amb_categories_exists' => Schema::hasTable('amb_categories'),
                ]);
                // #endregion
                Log::error('AMB migration create amb_games failed', [
                    'sessionId' => 'd5deb8',
                    'hypothesisId' => 'H5',
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }

        // #region agent log
        $debugLog('H3', 'amb migration completed', [
            'amb_categories' => Schema::hasTable('amb_categories'),
            'amb_products' => Schema::hasTable('amb_products'),
            'amb_games' => Schema::hasTable('amb_games'),
        ]);
        // #endregion
    }

    public function down(): void
    {
        Schema::dropIfExists('amb_games');
        Schema::dropIfExists('amb_products');
        Schema::dropIfExists('amb_categories');
    }
};
