<?php

namespace Database\Seeders;

use App\Models\ComponenteDinamico;
use App\Models\ComponenteValidacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponenteDinamicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Carga los componentes de un formulario específico
     *
     * @param string $formulario Nombre del formulario (ej: Mercurio30)
     * @return array
     */
    protected function cargarComponentes(string $formulario): array
    {
        $archivo = base_path("database/seeders/Componentes/{$formulario}Componentes.php");

        if (!file_exists($archivo)) {
            $this->command->warn("No se encontró el archivo de componentes para: {$formulario}");
            return [];
        }

        return require $archivo;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cargar componentes específicos de formularios
        $componentes = array_merge(
            $this->cargarComponentes('Mercurio30'),
            $this->cargarComponentes('Mercurio31'),
            $this->cargarComponentes('Mercurio32'),
            $this->cargarComponentes('Mercurio34'),
            $this->cargarComponentes('Mercurio36'),
            $this->cargarComponentes('Mercurio38'),
            $this->cargarComponentes('Mercurio39'),
            $this->cargarComponentes('Mercurio40'),
            $this->cargarComponentes('Mercurio41')
        );

        // Ordenar componentes por group_id y order
        usort($componentes, function ($a, $b) {
            if ($a['group_id'] == $b['group_id']) {
                return $a['order'] <=> $b['order'];
            }
            return $a['group_id'] <=> $b['group_id'];
        });

        // Insertar componentes en la base de datos
        foreach ($componentes as $componenteData) {
            $validacion = $componenteData['validacion'] ?? null;
            unset($componenteData['validacion']);
            $componente = ComponenteDinamico::create($componenteData);

            if ($validacion) {
                $componente->validacion()->create($validacion);
            }
        }
    }
}
