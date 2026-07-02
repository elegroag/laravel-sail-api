<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convierte columnas de radicado de texto a VARCHAR(20).
     *
     * - mercurio*: ruuid (originalmente CHAR(36) por uuid())
     * - radicados: radicado (originalmente CHAR(15))
     *
     * Formato del valor: XXX-YYYY-NNNNN (15 caracteres), generado por
     * App\Models\Adapter\HasCustomUuid.
     *
     * @var array<string, array{column: string, revert: string}>
     */
    protected array $tables = [
        'mercurio30' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio31' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio32' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio34' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio35' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio36' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio38' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio39' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio40' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio41' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'mercurio47' => ['column' => 'ruuid', 'revert' => 'CHAR(36)'],
        'radicados' => ['column' => 'radicado', 'revert' => 'CHAR(15)'],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $config) {
            $this->modifyColumn($table, $config['column'], 'VARCHAR(20)');
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $config) {
            $this->modifyColumn($table, $config['column'], $config['revert']);
        }
    }

    private function modifyColumn(string $table, string $column, string $targetType): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $current = DB::selectOne(
            'SELECT COLUMN_TYPE AS column_type
               FROM information_schema.columns
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME   = ?
                AND COLUMN_NAME  = ?',
            [$table, $column]
        );

        if (! $current) {
            return;
        }

        $currentType = strtoupper($current->column_type);
        $targetTypeUpper = strtoupper($targetType);

        if ($currentType === $targetTypeUpper) {
            return;
        }

        if ($targetTypeUpper === 'VARCHAR(20)' && preg_match('/^VARCHAR\((\d+)\)$/', $currentType, $m) && (int) $m[1] <= 20) {
            return;
        }

        // Cambio CHAR → VARCHAR requiere reconstrucción de tabla (ALGORITHM=COPY en MySQL 8).
        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$targetType}");
    }
};
