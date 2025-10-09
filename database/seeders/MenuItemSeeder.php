<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TABLE = 'menu_items';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->limpiarTabla();
            $menu_caja = [
                ['id' => 1, 'title' => 'Inicio', 'default_url' => 'cajas/principal/index', 'icon' => 'fas fa-home', 'color' => 'text-primary', 'nota' => 'texto', 'position' => 1, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 2, 'title' => 'Estadística', 'default_url' => 'cajas/principal/dashboard', 'icon' => 'ni ni-hat-3', 'color' => 'text-pink', 'nota' => 'texto', 'position' => 7, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 3, 'title' => 'Basicas', 'default_url' => null, 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-black', 'nota' => null, 'position' => 3, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 4, 'title' => 'Configuración', 'default_url' => null, 'icon' => 'ni ni-settings', 'color' => 'text-info', 'nota' => null, 'position' => 4, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 5, 'title' => 'Solicitudes empleadores', 'default_url' => null, 'icon' => 'ni ni-briefcase-24', 'color' => 'text-warning', 'nota' => null, 'position' => 5, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 6, 'title' => 'Solicitudes trabajadores', 'default_url' => null, 'icon' => 'ni ni-briefcase-24', 'color' => 'text-yellow', 'nota' => null, 'position' => 6, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 7, 'title' => 'Movimientos', 'default_url' => null, 'icon' => 'ni ni-active-40', 'color' => 'text-green', 'nota' => null, 'position' => 7, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 8, 'title' => 'Movile', 'default_url' => null, 'icon' => 'ni ni-controller', 'color' => 'text-blue', 'nota' => null, 'position' => 8, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 9, 'title' => 'Productos y servicios', 'default_url' => null, 'icon' => 'ni ni-box-2', 'color' => 'text-default', 'nota' => null, 'position' => 9, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 10, 'title' => 'Reportes', 'default_url' => null, 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-success', 'nota' => null, 'position' => 10, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 11, 'title' => 'Usuarios', 'default_url' => null, 'icon' => 'fas fa-users', 'color' => 'text-orange', 'nota' => null, 'position' => 11, 'parent_id' => null, 'is_visible' => 1, 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 12, 'title' => 'Basica', 'default_url' => 'cajas/mercurio01/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 13, 'title' => 'Datos Caja', 'default_url' => 'cajas/mercurio02/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 14, 'title' => 'Firmas', 'default_url' => 'cajas/mercurio03/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '3', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 15, 'title' => 'Tipos Acceso', 'default_url' => 'cajas/mercurio06/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '4', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 16, 'title' => 'Documentos', 'default_url' => 'cajas/mercurio12/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '5', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 17, 'title' => 'Permisos por usuario', 'default_url' => 'cajas/gener42/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '6', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 18, 'title' => 'Motivos Rechazo', 'default_url' => 'cajas/mercurio11/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '4', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 19, 'title' => 'Tipos Opciones', 'default_url' => 'cajas/mercurio09/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '4', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 20, 'title' => 'Oficinas', 'default_url' => 'cajas/mercurio04/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '3', 'parent_id' => '4', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 21, 'title' => 'Galería', 'default_url' => 'cajas/mercurio26/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '4', 'parent_id' => '4', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 22, 'title' => 'Empresas', 'default_url' => 'cajas/aprobacionemp/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '5', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 23, 'title' => 'Independientes', 'default_url' => 'cajas/aprobaindepen/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '5', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 24, 'title' => 'Pensionados', 'default_url' => 'cajas/aprobacionpen/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '3', 'parent_id' => '5', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 25, 'title' => 'Facultativos', 'default_url' => 'cajas/aprobacionfac/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '4', 'parent_id' => '5', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 26, 'title' => 'Madres Comunitarias', 'default_url' => 'cajas/aprobacioncom/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '5', 'parent_id' => '5', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 28, 'title' => 'Datos Basicos Empresa', 'default_url' => 'cajas/actualizaemp/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '7', 'parent_id' => '5', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 29, 'title' => 'Trabajadores', 'default_url' => 'cajas/aprobaciontra/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '6', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 30, 'title' => 'Conyuge', 'default_url' => 'cajas/aprobacioncon/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '6', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 31, 'title' => 'Beneficiario', 'default_url' => 'cajas/aprobacionben/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '3', 'parent_id' => '6', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 32, 'title' => 'Certificados', 'default_url' => 'cajas/aprobacioncer/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '4', 'parent_id' => '6', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 33, 'title' => 'Datos Basicos Trabajador', 'default_url' => 'cajas/actualizatra/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '5', 'parent_id' => '6', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 34, 'title' => 'Consulta de Auditoria', 'default_url' => 'cajas/consulta/consulta_auditoria_view', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '7', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 35, 'title' => 'Consulta de Activacion Masiva', 'default_url' => 'cajas/consulta/consulta_activacion_masiva_view', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '7', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 36, 'title' => 'Reasignar Solicitudes', 'default_url' => 'cajas/reasigna/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '3', 'parent_id' => '7', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 37, 'title' => 'Carga Laboral', 'default_url' => 'cajas/consulta/carga_laboral', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '4', 'parent_id' => '7', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 38, 'title' => 'Indicadores', 'default_url' => 'cajas/consulta/indicadores', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '5', 'parent_id' => '7', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 39, 'title' => 'Basica', 'default_url' => 'cajas/mercurio50/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 40, 'title' => 'Promociones', 'default_url' => 'cajas/mercurio57/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 41, 'title' => 'Turismo', 'default_url' => 'cajas/mercurio72/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '3', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 42, 'title' => 'Educación', 'default_url' => 'cajas/mercurio73/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '4', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 43, 'title' => 'Recreación', 'default_url' => 'cajas/mercurio74/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '5', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 44, 'title' => 'Destacadas', 'default_url' => 'cajas/mercurio53/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '6', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 45, 'title' => 'Categorias', 'default_url' => 'cajas/mercurio51/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '7', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 46, 'title' => 'Areas', 'default_url' => 'cajas/mercurio55/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '8', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 47, 'title' => 'Menu', 'default_url' => 'cajas/mercurio52/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '9', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 48, 'title' => 'Insfraestruturas', 'default_url' => 'cajas/mercurio56/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '10', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 49, 'title' => 'Clasificacion Comercios', 'default_url' => 'cajas/mercurio67/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '11', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 50, 'title' => 'Comercios', 'default_url' => 'cajas/mercurio65/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '12', 'parent_id' => '8', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 51, 'title' => 'Lista de productos', 'default_url' => 'cajas/admproductos/lista', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => '9', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 52, 'title' => 'Complemento nutricional', 'default_url' => 'cajas/admproductos/aplicados/27', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '2', 'parent_id' => '9', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 53, 'title' => 'Usuarios Enlinea', 'default_url' => 'cajas/usuario/index', 'icon' => null, 'color' => 'text-primary', 'nota' => null, 'position' => '1', 'parent_id' => '11', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 54, 'title' => 'Requerido por trabajador', 'default_url' => 'cajas/mercurio13/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '6', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 55, 'title' => 'Requerido por empresa', 'default_url' => 'cajas/mercurio14/index', 'icon' => null, 'color' => 'text-primary', 'nota' => 'texto', 'position' => '6', 'parent_id' => '3', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
                ['id' => 56, 'title' => 'Reportes solicitudes', 'default_url' => 'cajas/reportesol/index', 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-success', 'nota' => null, 'position' => '10', 'parent_id' => '10', 'is_visible' => '1', 'codapl' => 'CA', 'tipo' => 'A'],
            ];

            $menu_mercurio = [
                ['id' => 178, 'title' => 'Inicio', 'default_url' => 'mercurio/principal/index', 'icon' => 'fas fa-home', 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 179, 'title' => 'Reportar errores del sistema', 'default_url' => 'mercurio/notificaciones/index', 'icon' => 'fas fa-box', 'color' => 'text-red', 'nota' => 'texto', 'position' => '10', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 180, 'title' => 'Productos y servicios', 'default_url' => null, 'icon' => 'ni ni-briefcase-24', 'color' => 'text-orange', 'nota' => null, 'position' => '9', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 181, 'title' => 'Complemento nutricional', 'default_url' => 'mercurio/productos/complemento_nutricional', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '180', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 182, 'title' => 'Consultas', 'default_url' => null, 'icon' => 'ni ni-collection', 'color' => 'text-red', 'nota' => null, 'position' => '4', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 183, 'title' => 'Consulta nucleo familiar', 'default_url' => 'mercurio/subsidio/consulta_nucleo_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '182', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 184, 'title' => 'Consulta Giro', 'default_url' => 'mercurio/subsidio/consulta_giro_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '182', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 185, 'title' => 'Consulta No Giro', 'default_url' => 'mercurio/subsidio/consulta_no_giro_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '182', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 186, 'title' => 'Consulta Planilla', 'default_url' => 'mercurio/subsidio/consulta_planilla_trabajador_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '4', 'parent_id' => '182', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 187, 'title' => 'Consulta Saldo', 'default_url' => 'mercurio/subsidio/consulta_tarjeta', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '5', 'parent_id' => '182', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 188, 'title' => 'Movimientos', 'default_url' => null, 'icon' => 'ni ni-active-40', 'color' => 'text-info', 'nota' => null, 'position' => '5', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 189, 'title' => 'Actualizacion Datos Basicos', 'default_url' => 'mercurio/actualizadatostra/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '188', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 190, 'title' => 'Afiliacion Conyuge', 'default_url' => 'mercurio/conyuge/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '188', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 191, 'title' => 'Afiliacion Beneficiario', 'default_url' => 'mercurio/beneficiario/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '4', 'parent_id' => '188', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 192, 'title' => 'Presentación Certificados', 'default_url' => 'mercurio/certificados/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '5', 'parent_id' => '188', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 193, 'title' => 'Certificados', 'default_url' => null, 'icon' => 'ni ni-paper-diploma', 'color' => 'text-green', 'nota' => null, 'position' => '6', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 194, 'title' => 'Certificado de Afiliacion', 'default_url' => 'mercurio/subsidio/certificado_afiliacion', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '193', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 195, 'title' => 'Cuenta usuario', 'default_url' => null, 'icon' => 'fas fa-user', 'color' => 'text-orange', 'nota' => null, 'position' => '7', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 196, 'title' => 'Perfil Usuario', 'default_url' => 'mercurio/usuario/index', 'icon' => null, 'color' => '', 'nota' => null, 'position' => '1', 'parent_id' => '195', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 197, 'title' => 'Buscar Historial', 'default_url' => 'mercurio/subsidio/historial', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '195', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 198, 'title' => 'Firma Digital', 'default_url' => 'mercurio/firmas/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '195', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'T'],
                ['id' => 199, 'title' => 'Inicio', 'default_url' => 'mercurio/principal/index', 'icon' => 'fas fa-home', 'color' => 'text-info', 'nota' => 'texto', 'position' => '1', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 200, 'title' => 'Estadística', 'default_url' => 'mercurio/principal/dashboard_empresa', 'icon' => 'ni ni-hat-3', 'color' => 'text-yellow', 'nota' => 'texto', 'position' => '6', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 201, 'title' => 'Reportar errores del sistema', 'default_url' => 'mercurio/notificaciones/index', 'icon' => 'fas fa-box', 'color' => 'text-red', 'nota' => 'texto', 'position' => '10', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 202, 'title' => 'Consultas', 'default_url' => null, 'icon' => 'ni ni-collection', 'color' => 'text-red', 'nota' => null, 'position' => '4', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 203, 'title' => 'Consulta Trabajadores', 'default_url' => 'mercurio/subsidioemp/consulta_trabajadores_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '202', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 204, 'title' => 'Consulta Giro', 'default_url' => 'mercurio/subsidioemp/consulta_giro_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '202', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 205, 'title' => 'Consulta Mora Presunta', 'default_url' => 'mercurio/subsidioemp/consulta_mora_presunta', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '202', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 206, 'title' => 'Consulta Nomina', 'default_url' => 'mercurio/subsidioemp/consulta_nomina_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '4', 'parent_id' => '202', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 207, 'title' => 'Consulta Aportes', 'default_url' => 'mercurio/subsidioemp/consulta_aportes_view', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '5', 'parent_id' => '202', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 208, 'title' => 'Afiliaciones', 'default_url' => null, 'icon' => 'ni ni-active-40', 'color' => 'text-info', 'nota' => null, 'position' => '5', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 209, 'title' => 'Actualización Datos', 'default_url' => 'mercurio/actualizadatos/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '208', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 210, 'title' => 'Afiliacion de Trabajador', 'default_url' => 'mercurio/trabajador/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '208', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 211, 'title' => 'Afiliacion Conyuge', 'default_url' => 'mercurio/conyuge/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '208', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 212, 'title' => 'Afiliacion Beneficiario', 'default_url' => 'mercurio/beneficiario/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '4', 'parent_id' => '208', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 213, 'title' => 'Certificados', 'default_url' => null, 'icon' => 'ni ni-paper-diploma', 'color' => 'text-green', 'nota' => null, 'position' => '6', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 214, 'title' => 'Certificado de Afiliacion', 'default_url' => 'mercurio/subsidioemp/certificado_afiliacion', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '1', 'parent_id' => '213', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 215, 'title' => 'Certificado para el Trabajador', 'default_url' => 'mercurio/subsidioemp/certificado_para_trabajador', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '213', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 216, 'title' => 'Cuenta usuario', 'default_url' => null, 'icon' => 'fas fa-user', 'color' => 'text-orange', 'nota' => null, 'position' => '7', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 217, 'title' => 'Perfil Usuario', 'default_url' => 'mercurio/usuario/index', 'icon' => null, 'color' => '', 'nota' => null, 'position' => '1', 'parent_id' => '216', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 218, 'title' => 'Buscar Historial', 'default_url' => 'mercurio/subsidioemp/historial', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '216', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 219, 'title' => 'Firma Digital', 'default_url' => 'mercurio/firmas/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '216', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
                ['id' => 220, 'title' => 'Inicio', 'default_url' => 'mercurio/principal/index', 'icon' => 'fas fa-home', 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 221, 'title' => 'Reportar errores del sistema', 'default_url' => 'mercurio/notificaciones/index', 'icon' => 'fas fa-box', 'color' => 'text-red', 'nota' => 'texto', 'position' => '10', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 222, 'title' => 'Afiliaciones', 'default_url' => null, 'icon' => 'fas fa-box', 'color' => 'text-green', 'nota' => null, 'position' => '3', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 223, 'title' => 'Afiliación empresa', 'default_url' => 'mercurio/empresa/index', 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-blue', 'nota' => 'texto', 'position' => '1', 'parent_id' => '222', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 224, 'title' => 'Afiliación independientes', 'default_url' => 'mercurio/independiente/index', 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-blue', 'nota' => 'texto', 'position' => '2', 'parent_id' => '222', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 225, 'title' => 'Afiliación pensionados', 'default_url' => 'mercurio/pensionado/index', 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-blue', 'nota' => 'texto', 'position' => '3', 'parent_id' => '222', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 226, 'title' => 'Afiliación facultativos', 'default_url' => 'mercurio/facultativo/index', 'icon' => 'ni ni-bullet-list-67', 'color' => 'text-blue', 'nota' => 'texto', 'position' => '4', 'parent_id' => '222', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 227, 'title' => 'Cuenta usuario', 'default_url' => null, 'icon' => 'fas fa-user', 'color' => 'text-orange', 'nota' => null, 'position' => '4', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 228, 'title' => 'Perfil Usuario', 'default_url' => 'mercurio/usuario/index', 'icon' => null, 'color' => '', 'nota' => null, 'position' => '1', 'parent_id' => '227', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 229, 'title' => 'Buscar Historial', 'default_url' => 'mercurio/movimientos/historial', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '227', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 230, 'title' => 'Firma Digital', 'default_url' => 'mercurio/firmas/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '227', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'P'],
                ['id' => 231, 'title' => 'Inicio', 'default_url' => 'mercurio/principal/index', 'icon' => 'fas fa-home', 'color' => 'text-primary', 'nota' => 'texto', 'position' => '1', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 232, 'title' => 'Reportar errores del sistema', 'default_url' => 'mercurio/notificaciones/index', 'icon' => 'fas fa-box', 'color' => 'text-red', 'nota' => 'texto', 'position' => '10', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 233, 'title' => 'Foniñez', 'default_url' => null, 'icon' => 'fas fa-child', 'color' => 'text-yellow', 'nota' => null, 'position' => '3', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 234, 'title' => 'Beneficiarios', 'default_url' => 'mercurio/mercurio83/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '233', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 235, 'title' => 'Eventos', 'default_url' => 'mercurio/mercurio80/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '4', 'parent_id' => '233', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 236, 'title' => 'Cuenta usuario', 'default_url' => null, 'icon' => 'fas fa-user', 'color' => 'text-orange', 'nota' => null, 'position' => '4', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 237, 'title' => 'Perfil Usuario', 'default_url' => 'mercurio/usuario/index', 'icon' => null, 'color' => '', 'nota' => null, 'position' => '1', 'parent_id' => '236', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 238, 'title' => 'Buscar Historial', 'default_url' => 'mercurio/movimientos/historial', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '2', 'parent_id' => '236', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 239, 'title' => 'Firma Digital', 'default_url' => 'mercurio/firmas/index', 'icon' => null, 'color' => '', 'nota' => 'texto', 'position' => '3', 'parent_id' => '236', 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'F'],
                ['id' => 240, 'title' => 'Mi empresa', 'default_url' => 'mercurio/empresa/miempresa', 'icon' => 'fas fa-building', 'color' => 'text-black', 'nota' => 'texto', 'position' => '2', 'parent_id' => null, 'is_visible' => '1', 'codapl' => 'ME', 'tipo' => 'E'],
            ];

            foreach ($menu_mercurio as $item1) {
                MenuItem::create($item1);
            }

            foreach ($menu_caja as $item2) {
                MenuItem::create($item2);
            }
        });
    }

    /**
     * Elimina los registros existentes para permitir re-ejecuciones idempotentes.
     */
    protected function limpiarTabla(): void
    {
        DB::statement(sprintf('DELETE FROM %s', self::TABLE));
    }
}
