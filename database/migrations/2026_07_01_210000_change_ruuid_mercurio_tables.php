<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas objetivo: el campo ruuid se almacena como radicado de texto
     * (formato XXX-YYYY-NNNNN = 15 caracteres) generado por el trait
     * App\Models\Adapter\HasCustomUuid. Las migraciones originales lo
     * declararon como uuid() -> CHAR(36), lo que desperdicia espacio
     * y no refleja el tipo real del dato.
     *
     * Esta migración convierte ruuid de CHAR(36) a VARCHAR(20) en
     * cada tabla afectada, conservando todos los datos.
     */
    protected array $tables = [
        'mercurio30',
        'mercurio31',
        'mercurio32',
        'mercurio34',
        'mercurio35',
        'mercurio36',
        'mercurio38',
        'mercurio39',
        'mercurio40',
        'mercurio41',
        'mercurio47',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $column = DB::selectOne(
                'SELECT COLUMN_TYPE AS column_type
                   FROM information_schema.columns
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME   = ?
                    AND COLUMN_NAME  = ?',
                [$table, 'ruuid']
            );

            if (! $column) {
                continue;
            }

            $currentType = strtoupper($column->column_type);

            // Si ya es VARCHAR(20) (o menor) dejamos la tabla como está
            if (preg_match('/^VARCHAR\((\d+)\)$/', $currentType, $m) && (int) $m[1] <= 20) {
                continue;
            }

            // ALTER en línea: no bloquea lecturas/escrituras en MySQL 8.0
            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` VARCHAR(20), ALGORITHM=INPLACE, LOCK=NONE");
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $column = DB::selectOne(
                'SELECT COLUMN_TYPE AS column_type
                   FROM information_schema.columns
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME   = ?
                    AND COLUMN_NAME  = ?',
                [$table, 'ruuid']
            );

            if (! $column) {
                continue;
            }

            $currentType = strtoupper($column->column_type);

            if ($currentType === 'CHAR(36)') {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `ruuid` CHAR(36), ALGORITHM=INPLACE, LOCK=NONE");
        }
    }
};
