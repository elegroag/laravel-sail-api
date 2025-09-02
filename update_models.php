<?php

// Script para agregar $table, $timestamps=false y $primaryKey a los modelos que usan ModelBase
// Ejecutar desde la raíz del proyecto

require_once __DIR__ . '/vendor/autoload.php'; // Para usar Str de Laravel

use Illuminate\Support\Str;

$modelsDir = __DIR__ . '/app/Models/';
$files = scandir($modelsDir);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'Adapter' && !is_dir($modelsDir . $file)) {
        $filePath = $modelsDir . $file;
        $content = file_get_contents($filePath);

        // Verificar si extiende ModelBase
        if (strpos($content, 'extends ModelBase') === false) {
            continue;
        }

        // Obtener el nombre de la clase
        preg_match('/class (\w+) extends ModelBase/', $content, $classMatch);
        if (!$classMatch) continue;
        $className = $classMatch[1];

        // Determinar el nombre de la tabla
        $tableName = Str::plural(Str::snake($className));

        $additions = [];

        // Agregar $table si no existe
        if (strpos($content, 'protected $table') === false) {
            $additions[] = "    protected \$table = '$tableName';";
        }

        // Agregar $timestamps = false si no existe
        if (strpos($content, '$timestamps') === false) {
            $additions[] = "    public \$timestamps = false;";
        }

        // Agregar $primaryKey si no existe
        if (strpos($content, 'protected $primaryKey') === false) {
            $additions[] = "    protected \$primaryKey = 'id';";
        }

        if (empty($additions)) {
            echo "El modelo $file ya tiene las propiedades necesarias.\n";
            continue;
        }

        // Agregar después de fillable o después de la declaración de clase
        $insertionPoint = '';
        if (strpos($content, 'protected $fillable') !== false) {
            $insertionPoint = 'protected $fillable';
        } else {
            $insertionPoint = 'class ' . $className . ' extends ModelBase';
        }

        $addStr = "\n" . implode("\n", $additions) . "\n\n";
        $newContent = str_replace($insertionPoint, $insertionPoint . $addStr, $content);

        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            echo "Agregado propiedades a $file.\n";
        } else {
            echo "No se pudo agregar a $file.\n";
        }
    }
}

echo "Proceso completado.\n";
