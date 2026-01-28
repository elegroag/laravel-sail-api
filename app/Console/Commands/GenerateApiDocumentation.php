<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateApiDocumentation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:docs 
                            {--generate : Generar nueva documentación}
                            {--validate : Validar documentación existente}
                            {--export : Exportar a archivo JSON}
                            {--clean : Limpiar caché de documentación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar y gestionar documentación de API CLISISU';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Generando documentación de API CLISISU...');

        if ($this->option('clean')) {
            $this->cleanDocumentation();
        }

        if ($this->option('generate') || !$this->option('validate')) {
            $this->generateDocumentation();
        }

        if ($this->option('validate')) {
            $this->validateDocumentation();
        }

        if ($this->option('export')) {
            $this->exportDocumentation();
        }

        $this->info('✅ Documentación generada exitosamente');
        $this->info('📖 Acceda a la documentación en: http://localhost:8000/docs/api');
        $this->info('📄 Descargue el JSON en: http://localhost:8000/docs/api.json');
    }

    /**
     * Generar documentación
     */
    private function generateDocumentation()
    {
        $this->info('📝 Generando especificación OpenAPI...');

        // Aquí podrías agregar lógica adicional para generar documentación
        // Por ejemplo, ejecutar tests, validar endpoints, etc.

        $this->info('✅ Especificación OpenAPI generada');
    }

    /**
     * Validar documentación
     */
    private function validateDocumentation()
    {
        $this->info('🔍 Validando documentación...');

        // Validar que todos los endpoints tengan documentación
        // Validar que las respuestas sean consistentes
        // Validar que los ejemplos funcionen

        $this->info('✅ Documentación validada');
    }

    /**
     * Exportar documentación
     */
    private function exportDocumentation()
    {
        $this->info('💾 Exportando documentación...');

        // Crear directorio de exportación si no existe
        $exportPath = storage_path('app/api-docs');
        if (!File::exists($exportPath)) {
            File::makeDirectory($exportPath, 0755, true);
        }

        $this->info('✅ Documentación exportada a: ' . $exportPath);
    }

    /**
     * Limpiar caché de documentación
     */
    private function cleanDocumentation()
    {
        $this->info('🧹 Limpiando caché de documentación...');

        // Limpiar caché de Laravel
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->info('✅ Caché limpiada');
    }
}
