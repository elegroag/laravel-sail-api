<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Empresa;

class ModelBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_maximum_and_minimum_scalar()
    {
        // create empresas with known numero_empleados (provide required fields)
        Empresa::create([
            'nombre' => 'E1',
            'rut' => '11111111-1',
            'direccion' => 'Dir 1',
            'numero_empleados' => 5,
        ]);
        Empresa::create([
            'nombre' => 'E2',
            'rut' => '22222222-2',
            'direccion' => 'Dir 2',
            'numero_empleados' => 10,
        ]);
        Empresa::create([
            'nombre' => 'E3',
            'rut' => '33333333-3',
            'direccion' => 'Dir 3',
            'numero_empleados' => 3,
        ]);

        $empresa = new Empresa();

        $max = $empresa->maximum('numero_empleados');
        $min = $empresa->minimum('numero_empleados');

        $this->assertEquals(10, $max);
        $this->assertEquals(3, $min);
    }

    public function test_grouped_maximum_and_minimum()
    {
        // create empresas across two sectors
        Empresa::create([
            'nombre' => 'EA1',
            'rut' => '44444444-4',
            'direccion' => 'Dir A1',
            'sector_economico' => 'A',
            'numero_empleados' => 4,
        ]);
        Empresa::create([
            'nombre' => 'EA2',
            'rut' => '55555555-5',
            'direccion' => 'Dir A2',
            'sector_economico' => 'A',
            'numero_empleados' => 7,
        ]);
        Empresa::create([
            'nombre' => 'EB1',
            'rut' => '66666666-6',
            'direccion' => 'Dir B1',
            'sector_economico' => 'B',
            'numero_empleados' => 2,
        ]);
        Empresa::create([
            'nombre' => 'EB2',
            'rut' => '77777777-7',
            'direccion' => 'Dir B2',
            'sector_economico' => 'B',
            'numero_empleados' => 9,
        ]);

        $empresa = new Empresa();

        $groupedMax = $empresa->maximum('numero_empleados', 'group:sector_economico');
        $groupedMin = $empresa->minimum('numero_empleados', 'group:sector_economico');

        // expect collection with two groups
        $this->assertCount(2, $groupedMax);
        $this->assertCount(2, $groupedMin);

        // convert to associative maps by sector
        $mapMax = [];
        foreach ($groupedMax as $row) {
            $mapMax[$row->sector_economico] = (int)$row->maximum;
        }
        $mapMin = [];
        foreach ($groupedMin as $row) {
            $mapMin[$row->sector_economico] = (int)$row->minimum;
        }

        $this->assertEquals(7, $mapMax['A']);
        $this->assertEquals(9, $mapMax['B']);
        $this->assertEquals(4, $mapMin['A']);
        $this->assertEquals(2, $mapMin['B']);
    }

    public function test_find_and_findFirst()
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

        $empresa = new Empresa();

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

    public function test_findAllBySql_and_findBySql()
    {
        Empresa::create([
            'nombre' => 'S1',
            'rut' => '12121212-1',
            'direccion' => 'Dir S1',
            'numero_empleados' => 11,
        ]);
        Empresa::create([
            'nombre' => 'S2',
            'rut' => '13131313-1',
            'direccion' => 'Dir S2',
            'numero_empleados' => 4,
        ]);

        $empresa = new Empresa();

        $all = $empresa->findAllBySql('SELECT nombre, numero_empleados FROM empresas WHERE numero_empleados >= 5');
        $this->assertGreaterThanOrEqual(1, $all->count());
        $this->assertEquals('S1', $all[0]->nombre);

        $one = $empresa->findBySql('SELECT nombre, numero_empleados FROM empresas WHERE numero_empleados = 4');
        $this->assertInstanceOf(Empresa::class, $one);
        $this->assertEquals('S2', $one->nombre);
    }

    public function test_updateAll_updates_records()
    {
        // create empresas
        Empresa::create([
            'nombre' => 'U1',
            'rut' => '20000000-1',
            'direccion' => 'Dir U1',
            'numero_empleados' => 5,
        ]);
        Empresa::create([
            'nombre' => 'U2',
            'rut' => '20000000-2',
            'direccion' => 'Dir U2',
            'numero_empleados' => 10,
        ]);

        $empresa = new Empresa();

        // update a single record using set: and conditions:
        $affected = $empresa->updateAll('set:numero_empleados=42', "conditions:rut='20000000-1'");
        $this->assertEquals(1, $affected);

        $fresh = Empresa::where('rut', '20000000-1')->first();
        $this->assertEquals(42, $fresh->numero_empleados);

        // update multiple rows using array form
        $affected2 = $empresa->updateAll('set:numero_empleados=99', "conditions:numero_empleados>=10");
        $this->assertGreaterThanOrEqual(1, $affected2);
        $this->assertEquals(99, Empresa::where('rut', '20000000-2')->first()->numero_empleados);
    }

    public function test_deleteAll_deletes_records()
    {
        Empresa::create([
            'nombre' => 'D1',
            'rut' => '30000000-1',
            'direccion' => 'Dir D1',
            'numero_empleados' => 1,
        ]);
        Empresa::create([
            'nombre' => 'D2',
            'rut' => '30000000-2',
            'direccion' => 'Dir D2',
            'numero_empleados' => 2,
        ]);

        $empresa = new Empresa();
        $deleted = $empresa->deleteAll("conditions:numero_empleados=1");
        $this->assertEquals(1, $deleted);
        $this->assertNull(Empresa::where('rut', '30000000-1')->first());

        // ensure deleteAll prevents full-table delete when no conditions
        Empresa::create([
            'nombre' => 'D3',
            'rut' => '30000000-3',
            'direccion' => 'Dir D3',
            'numero_empleados' => 3,
        ]);
        $deletedNone = $empresa->deleteAll();
        $this->assertEquals(0, $deletedNone);
    }
}
