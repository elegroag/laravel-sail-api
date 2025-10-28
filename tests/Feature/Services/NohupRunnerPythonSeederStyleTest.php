<?php

namespace Tests\Feature\Services;

use App\Models\ComandoEstructuras;
use App\Services\NohupRunner;
use Database\Seeders\ComandoEstructuraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NohupRunnerPythonSeederStyleTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    #[Test]
    public function ejecuta_python_con_estructura_y_variables_al_estilo_seeder(): void
    {
        $this->seed(ComandoEstructuraSeeder::class);

        // Saltar si python3 no está disponible en el entorno
        $python = trim((string) shell_exec('command -v python3 || which python3 || true'));
        if ($python === '') {
            $this->markTestSkipped('python3 no está disponible en el entorno de pruebas.');
        }

        // Script temporal de python que imprime dos argumentos
        $dir = storage_path('app/testscripts');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $script = $dir.'/test_args.py';
        file_put_contents($script, "import sys\nprint(sys.argv[1] + ' ' + sys.argv[2])\n");
        @chmod($script, 0644);

        $runner = app(NohupRunner::class);
        $cmd = $runner->dispatch('py_sync_seeder_style', [
            'script' => $script,
            'arg1' => 'Hola',
            'arg2' => 'Mundo',
        ], 1);

        $this->assertEquals('F', $cmd->estado);
        $this->assertEquals(100, (int)$cmd->progreso);
        $this->assertIsString($cmd->resultado);
        $this->assertFileExists($cmd->resultado);
        $log = file_get_contents($cmd->resultado);
        $this->assertStringContainsString('Hola Mundo', $log);
    }
}
