<?php

namespace Tests\Unit;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_and_find_first()
    {
        // create sample empresas
        Empresa::create([
            'nombre' => 'F1',
            'rut' => '88888888-8',
            'direccion' => 'Dir F1',
            'numero_empleados' => 6,
        ]);
        Empresa::create([
            'nombre' => 'F2',
            'rut' => '99999999-9',
            'direccion' => 'Dir F2',
            'numero_empleados' => 2,
        ]);
        Empresa::create([
            'nombre' => 'F3',
            'rut' => '10101010-1',
            'direccion' => 'Dir F3',
            'numero_empleados' => 8,
        ]);

        $empresa = new Empresa;

        // find those with numero_empleados > 4, ordered asc
        $results = $empresa->find('conditions:numero_empleados > 4', 'order:numero_empleados ASC');
        $this->assertCount(2, $results);
        $this->assertEquals(6, $results[0]->numero_empleados);
        $this->assertEquals(8, $results[1]->numero_empleados);

        // findFirst exact match
        $first = $empresa->findFirst('conditions:numero_empleados = 2');
        $this->assertInstanceOf(Empresa::class, $first);
        $this->assertEquals('F2', $first->nombre);
    }
}
