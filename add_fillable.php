<?php

// Script para agregar fillable a los modelos que usan ModelBase
// Ejecutar desde la raíz del proyecto

$modelsDir = __DIR__ . '/app/Models/';
$files = scandir($modelsDir);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'Adapter') {
        $filePath = $modelsDir . $file;
        $content = file_get_contents($filePath);

        // Verificar si ya tiene fillable
        if (strpos($content, 'protected $fillable') !== false) {
            echo "El modelo $file ya tiene fillable.\n";
            continue;
        }

        // Verificar si extiende ModelBase
        if (strpos($content, 'extends ModelBase') === false) {
            echo "El modelo $file no extiende ModelBase.\n";
            continue;
        }

        // Extraer propiedades protected
        preg_match_all('/protected \$([a-zA-Z_][a-zA-Z0-9_]*);/', $content, $matches);
        $properties = $matches[1];

        // Excluir 'id' del fillable
        $fillable = array_filter($properties, function($prop) {
            return $prop !== 'id';
        });

        if (empty($fillable)) {
            echo "No se encontraron propiedades fillable en $file.\n";
            continue;
        }

        // Crear el array fillable
        $fillableStr = "    protected \$fillable = [\n";
        foreach ($fillable as $field) {
            $fillableStr .= "        '$field',\n";
        }
        $fillableStr .= "    ];\n\n";

        // Agregar después de la declaración de clase
        $pattern = '/(class \w+ extends ModelBase\s*\{)/';
        $replacement = "$1\n\n$fillableStr";
        $newContent = preg_replace($pattern, $replacement, $content);

        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            echo "Agregado fillable a $file.\n";
        } else {
            echo "No se pudo agregar fillable a $file.\n";
        }
    }
}

echo "Proceso completado.\n";
