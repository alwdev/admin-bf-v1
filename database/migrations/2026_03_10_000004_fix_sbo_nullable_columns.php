<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function makeColumnNullable(string $table, string $column): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return;
        }

        $dbName = DB::connection()->getDatabaseName();
        $col = DB::table('information_schema.COLUMNS')
            ->select([
                'COLUMN_TYPE',
                'CHARACTER_SET_NAME',
                'COLLATION_NAME',
                'COLUMN_DEFAULT',
                'EXTRA',
            ])
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->first();

        if (!$col || empty($col->COLUMN_TYPE)) {
            return;
        }

        $typeSql = $col->COLUMN_TYPE;
        if (!empty($col->CHARACTER_SET_NAME) && !empty($col->COLLATION_NAME)) {
            $typeSql .= ' CHARACTER SET ' . $col->CHARACTER_SET_NAME . ' COLLATE ' . $col->COLLATION_NAME;
        }

        $defaultSql = '';
        if ($col->COLUMN_DEFAULT !== null) {
            $def = (string) $col->COLUMN_DEFAULT;
            $upper = strtoupper($def);
            if ($upper === 'CURRENT_TIMESTAMP' || $upper === 'CURRENT_TIMESTAMP()' || $upper === 'NULL') {
                $defaultSql = ' DEFAULT ' . $def;
            } elseif (is_numeric($def)) {
                $defaultSql = ' DEFAULT ' . $def;
            } else {
                $defaultSql = " DEFAULT '" . str_replace("'", "''", $def) . "'";
            }
        }

        $extraSql = '';
        if (!empty($col->EXTRA) && stripos((string) $col->EXTRA, 'on update') !== false) {
            $extraSql = ' ' . $col->EXTRA;
        }

        try {
            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$typeSql} NULL{$defaultSql}{$extraSql}");
        } catch (\Throwable $e) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$typeSql} NULL");
        }
    }

    public function up(): void
    {
        foreach (['gpid', 'game_id', 'game_code', 'provider_type', 'img', 'payload'] as $col) {
            $this->makeColumnNullable('sbo_game_lists', $col);
        }

        $this->makeColumnNullable('sbo_providers', 'gpid');
    }

    public function down(): void
    {
    }
};

