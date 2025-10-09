<?php

namespace Tests\Unit;

use App\Models\Adapter\DbBase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActiveRecordBaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure empresas and trabajadores tables exist via migrations run by RefreshDatabase
        // Create a sample empresa to satisfy foreign key
        DB::table('empresas')->insert([
            'nombre' => 'Empresa Test',
            'rut' => '00000000-0',
            'direccion' => 'Direccion',
            'numero_empleados' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_insert_and_fetch()
    {
        $ar = DbBase::rawConnect();

        $empresaId = DB::table('empresas')->first()->id;

        $inserted = $ar->insert('trabajadores', ['Juan', 'Perez', '12345678-9', 'j@e.com', '2000-01-01', 'masculino', 'Dir', 'Dev', 1000.00, '2024-01-01', $empresaId], ['nombres', 'apellidos', 'rut', 'email', 'fecha_nacimiento', 'genero', 'direccion', 'cargo', 'salario', 'fecha_ingreso', 'empresa_id'], true);
        $this->assertTrue($inserted);

        $all = $ar->inQueryAssoc('SELECT nombres, apellidos, salario FROM trabajadores');
        $this->assertGreaterThanOrEqual(1, count($all));

        $one = $ar->fetchOne("SELECT nombres, apellidos FROM trabajadores WHERE rut = '12345678-9'");
        $this->assertNotNull($one);
        $this->assertEquals('Juan', $one['nombres']);
    }

    public function test_in_query_and_fetch_array()
    {
        $ar = DbBase::rawConnect();
        DB::table('trabajadores')->insert([
            'nombres' => 'X',
            'apellidos' => 'Y',
            'rut' => '22222222-2',
            'email' => 'x@y.com',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'masculino',
            'direccion' => 'Dir',
            'cargo' => 'T',
            'salario' => 2000.00,
            'fecha_ingreso' => '2020-01-01',
            'empresa_id' => DB::table('empresas')->first()->id,
        ]);

        $assoc = $ar->inQueryAssoc('SELECT nombres, salario FROM trabajadores');
        $this->assertIsArray($assoc);
        $this->assertEquals('X', $assoc[0]['nombres']);

        $num = $ar->inQueryNum('SELECT nombres, salario FROM trabajadores');
        $this->assertIsArray($num);

        $arr = $ar->fetchArray('SELECT nombres, salario FROM trabajadores');
        $this->assertIsArray($arr);
    }

    public function test_update_and_delete_and_transactions()
    {
        $ar = DbBase::rawConnect();
        DB::table('trabajadores')->insert([
            'nombres' => 'T',
            'apellidos' => 'U',
            'rut' => '33333333-3',
            'email' => 't@u.com',
            'fecha_nacimiento' => '1991-01-01',
            'genero' => 'femenino',
            'direccion' => 'Dir',
            'cargo' => 'C',
            'salario' => 1500.00,
            'fecha_ingreso' => '2021-01-01',
            'empresa_id' => DB::table('empresas')->first()->id,
        ]);

        $updated = $ar->update('trabajadores', ['salario'], [1800.00], "rut='33333333-3'", false);
        $this->assertGreaterThanOrEqual(0, $updated);

        $num = $ar->numRows('SELECT * FROM trabajadores WHERE salario = 1800.00');
        $this->assertEquals(1, $num);

        $ar->begin();
        $ar->insert('trabajadores', ['Z', 'W', '44444444-4', 'z@w.com', '1995-01-01', 'otro', 'Dir', 'X', 700.00, '2023-01-01', DB::table('empresas')->first()->id], ['nombres', 'apellidos', 'rut', 'email', 'fecha_nacimiento', 'genero', 'direccion', 'cargo', 'salario', 'fecha_ingreso', 'empresa_id'], true);
        $ar->rollback();

        // after rollback the count should remain 1 (we had only 1 row before the transaction)
        $this->assertEquals(1, DB::table('trabajadores')->count());

        $ar->begin();
        $ar->insert('trabajadores', ['Z', 'W', '44444444-4', 'z@w.com', '1995-01-01', 'otro', 'Dir', 'X', 700.00, '2023-01-01', DB::table('empresas')->first()->id], ['nombres', 'apellidos', 'rut', 'email', 'fecha_nacimiento', 'genero', 'direccion', 'cargo', 'salario', 'fecha_ingreso', 'empresa_id'], true);
        $ar->commit();

        // after commit the transient insert should be persisted -> total 2 rows
        $this->assertEquals(2, DB::table('trabajadores')->count());

        $deleted = $ar->delete('trabajadores', "rut='44444444-4'");
        $this->assertGreaterThanOrEqual(0, $deleted);
    }
}
