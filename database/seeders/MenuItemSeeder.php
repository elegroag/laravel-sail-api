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
            $menuItems = [
                ["id" => 1, "title" => "Inicio", "default_url" => "principal/index", "icon" => "fas fa-home", "color" => "text-primary", "nota" => "texto", "position" => 1, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 2, "title" => "Estadística", "default_url" => "principal/dashboard", "icon" => "ni ni-hat-3", "color" => "text-yellow", "nota" => "texto", "position" => 6, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 3, "title" => "Basicas", "default_url" => NULL, "icon" => "ni ni-bullet-list-67", "color" => "text-default", "nota" => NULL, "position" => 3, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 4, "title" => "Configuración", "default_url" => NULL, "icon" => "ni ni-settings", "color" => "text-info", "nota" => NULL, "position" => 4, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 5, "title" => "Solicitudes Empleadores", "default_url" => NULL, "icon" => "ni ni-briefcase-24", "color" => "text-warning", "nota" => NULL, "position" => 5, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 6, "title" => "Solicitudes Trabajadores", "default_url" => NULL, "icon" => "ni ni-briefcase-24", "color" => "text-yellow", "nota" => NULL, "position" => 6, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 7, "title" => "Movimientos", "default_url" => NULL, "icon" => "ni ni-active-40", "color" => "text-green", "nota" => NULL, "position" => 7, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 8, "title" => "Movile", "default_url" => NULL, "icon" => "ni ni-controller", "color" => "text-blue", "nota" => NULL, "position" => 8, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 9, "title" => "Productos y servicios", "default_url" => NULL, "icon" => "ni ni-box-2", "color" => "text-default", "nota" => NULL, "position" => 9, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 10, "title" => "Reportes", "default_url" => NULL, "icon" => "ni ni-bullet-list-67", "color" => "text-success", "nota" => NULL, "position" => 10, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 11, "title" => "Usuarios", "default_url" => NULL, "icon" => "fas fa-users", "color" => "text-orange", "nota" => NULL, "position" => 11, "parent_id" => NULL, "is_visible" => 1, "codapl" => "CA", "tipo" => "A"],
                ["id" => 12, "title" => "Basica", "default_url" => "mercurio01/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 13, "title" => "Datos Caja", "default_url" => "mercurio02/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 14, "title" => "Firmas", "default_url" => "mercurio03/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "3", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 15, "title" => "Tipos Acceso", "default_url" => "mercurio06/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "4", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 16, "title" => "Documentos", "default_url" => "mercurio12/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "5", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 17, "title" => "Permisos por usuario", "default_url" => "gener42/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "6", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 18, "title" => "Motivos Rechazo", "default_url" => "mercurio11/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "4", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 19, "title" => "Tipos Opciones", "default_url" => "mercurio09/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "4", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 20, "title" => "Oficinas", "default_url" => "mercurio04/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "3", "parent_id" => "4", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 21, "title" => "Galería", "default_url" => "mercurio26/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "4", "parent_id" => "4", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 22, "title" => "Empresas", "default_url" => "aprobacionemp/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 23, "title" => "Independientes", "default_url" => "aprobaindepen/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 24, "title" => "Pensionados", "default_url" => "aprobacionpen/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "3", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 25, "title" => "Facultativos", "default_url" => "aprobacionfac/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "4", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 26, "title" => "Madres Comunitarias", "default_url" => "aprobacioncom/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "5", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 27, "title" => "Servicio domestico", "default_url" => "aprobaciondom/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "6", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 28, "title" => "Datos Basicos Empresa", "default_url" => "actualizardatos/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "7", "parent_id" => "5", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 29, "title" => "Trabajadores", "default_url" => "aprobaciontra/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "6", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 30, "title" => "Conyuge", "default_url" => "aprobacioncon/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "6", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 31, "title" => "Beneficiario", "default_url" => "aprobacionben/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "3", "parent_id" => "6", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 32, "title" => "Certificados", "default_url" => "aprobacioncer/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "4", "parent_id" => "6", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 33, "title" => "Datos Basicos Trabajador", "default_url" => "aprobaciondatos/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "5", "parent_id" => "6", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 34, "title" => "Consulta de Auditoria", "default_url" => "consulta/consulta_auditoria_view", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "7", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 35, "title" => "Consulta de Activacion Masiva", "default_url" => "consulta/consulta_activacion_masiva_view", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "7", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 36, "title" => "Reasignar Solicitudes", "default_url" => "reasigna/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "3", "parent_id" => "7", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 37, "title" => "Carga Laboral", "default_url" => "consulta/carga_laboral", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "4", "parent_id" => "7", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 38, "title" => "Indicadores", "default_url" => "consulta/indicadores", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "5", "parent_id" => "7", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 39, "title" => "Basica", "default_url" => "mercurio50/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 40, "title" => "Promociones", "default_url" => "mercurio57/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 41, "title" => "Turismo", "default_url" => "mercurio72/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "3", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 42, "title" => "Educación", "default_url" => "mercurio73/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "4", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 43, "title" => "Recreación", "default_url" => "mercurio74/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "5", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 44, "title" => "Destacadas", "default_url" => "mercurio53/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "6", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 45, "title" => "Categorias", "default_url" => "mercurio51/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "7", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 46, "title" => "Areas", "default_url" => "mercurio55/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "8", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 47, "title" => "Menu", "default_url" => "mercurio52/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "9", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 48, "title" => "Insfraestruturas", "default_url" => "mercurio56/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "10", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 49, "title" => "Clasificacion Comercios", "default_url" => "mercurio67/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "11", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 50, "title" => "Comercios", "default_url" => "mercurio65/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "12", "parent_id" => "8", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 51, "title" => "Lista de productos", "default_url" => "admproductos/lista", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => "9", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 52, "title" => "Complemento nutricional", "default_url" => "admproductos/aplicados/27", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "2", "parent_id" => "9", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 53, "title" => "Usuarios Enlinea", "default_url" => "usuario/index", "icon" => NULL, "color" => "text-primary", "nota" => NULL, "position" => "1", "parent_id" => "11", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 54, "title" => "Requerido por trabajador", "default_url" => "mercurio13/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "6", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 55, "title" => "Requerido por empresa", "default_url" => "mercurio14/index", "icon" => NULL, "color" => "text-primary", "nota" => "texto", "position" => "6", "parent_id" => "3", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 56, "title" => "Reportes solicitudes", "default_url" => "reportesol/index", "icon" => "ni ni-bullet-list-67", "color" => "text-success", "nota" => NULL, "position" => "10", "parent_id" => "10", "is_visible" => "1", "codapl" => "CA", "tipo" => "A"],
                ["id" => 178, "title" => "Inicio", "default_url" => "principal/index", "icon" => "fas fa-home", "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 179, "title" => "Reportar errores del sistema", "default_url" => "notificaciones/index", "icon" => "fas fa-box", "color" => "text-red", "nota" => "texto", "position" => "10", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 180, "title" => "Productos y servicios", "default_url" => NULL, "icon" => "ni ni-briefcase-24", "color" => "text-orange", "nota" => NULL, "position" => "9", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 181, "title" => "Complemento nutricional", "default_url" => "productos/complemento_nutricional", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "180", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 182, "title" => "Consultas", "default_url" => NULL, "icon" => "ni ni-collection", "color" => "text-red", "nota" => NULL, "position" => "4", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 183, "title" => "Consulta nucleo familiar", "default_url" => "subsidio/consulta_nucleo_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "182", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 184, "title" => "Consulta Giro", "default_url" => "subsidio/consulta_giro_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "182", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 185, "title" => "Consulta No Giro", "default_url" => "subsidio/consulta_no_giro_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "182", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 186, "title" => "Consulta Planilla", "default_url" => "subsidio/consulta_planilla_trabajador_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "4", "parent_id" => "182", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 187, "title" => "Consulta Saldo", "default_url" => "subsidio/consulta_tarjeta", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "5", "parent_id" => "182", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 188, "title" => "Movimientos", "default_url" => NULL, "icon" => "ni ni-active-40", "color" => "text-info", "nota" => NULL, "position" => "5", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 189, "title" => "Actualizacion Datos Basicos", "default_url" => "actualizadatostra/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "188", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 190, "title" => "Afiliacion Conyuge", "default_url" => "conyuge/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "188", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 191, "title" => "Afiliacion Beneficiario", "default_url" => "beneficiario/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "4", "parent_id" => "188", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 192, "title" => "Presentación Certificados", "default_url" => "certificados/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "5", "parent_id" => "188", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 193, "title" => "Certificados", "default_url" => NULL, "icon" => "ni ni-paper-diploma", "color" => "text-green", "nota" => NULL, "position" => "6", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 194, "title" => "Certificado de Afiliacion", "default_url" => "subsidio/certificado_afiliacion_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "193", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 195, "title" => "Cuenta usuario", "default_url" => NULL, "icon" => "fas fa-user", "color" => "text-orange", "nota" => NULL, "position" => "7", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 196, "title" => "Perfil Usuario", "default_url" => "usuario/index", "icon" => NULL, "color" => "", "nota" => NULL, "position" => "1", "parent_id" => "195", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 197, "title" => "Buscar Historial", "default_url" => "movimientos/historial", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "195", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 198, "title" => "Firma Digital", "default_url" => "firmas/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "195", "is_visible" => "1", "codapl" => "ME", "tipo" => "T"],
                ["id" => 199, "title" => "Inicio", "default_url" => "principal/index", "icon" => "fas fa-home", "color" => "text-info", "nota" => "texto", "position" => "1", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 200, "title" => "Estadística", "default_url" => "principal/dashboard_empresa", "icon" => "ni ni-hat-3", "color" => "text-yellow", "nota" => "texto", "position" => "6", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 201, "title" => "Reportar errores del sistema", "default_url" => "notificaciones/index", "icon" => "fas fa-box", "color" => "text-red", "nota" => "texto", "position" => "10", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 202, "title" => "Consultas", "default_url" => NULL, "icon" => "ni ni-collection", "color" => "text-red", "nota" => NULL, "position" => "4", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 203, "title" => "Consulta Trabajadores", "default_url" => "subsidioemp/consulta_trabajadores_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "202", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 204, "title" => "Consulta Giro", "default_url" => "subsidioemp/consulta_giro_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "202", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 205, "title" => "Consulta Mora Presunta", "default_url" => "subsidioemp/consulta_mora_presunta", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "202", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 206, "title" => "Consulta Nomina", "default_url" => "subsidioemp/consulta_nomina_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "4", "parent_id" => "202", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 207, "title" => "Consulta Aportes", "default_url" => "subsidioemp/consulta_aportes_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "5", "parent_id" => "202", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 208, "title" => "Afiliaciones", "default_url" => NULL, "icon" => "ni ni-active-40", "color" => "text-info", "nota" => NULL, "position" => "5", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 209, "title" => "Actualización Datos", "default_url" => "actualizadatos/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "208", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 210, "title" => "Afiliacion de Trabajador", "default_url" => "trabajador/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "208", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 211, "title" => "Afiliacion Conyuge", "default_url" => "conyuge/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "208", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 212, "title" => "Afiliacion Beneficiario", "default_url" => "beneficiario/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "4", "parent_id" => "208", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 213, "title" => "Certificados", "default_url" => NULL, "icon" => "ni ni-paper-diploma", "color" => "text-green", "nota" => NULL, "position" => "6", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 214, "title" => "Certificado de Afiliacion", "default_url" => "subsidioemp/certificado_afiliacion_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "1", "parent_id" => "213", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 215, "title" => "Certificado para el Trabajador", "default_url" => "subsidioemp/certificado_para_trabajador_view", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "213", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 216, "title" => "Cuenta usuario", "default_url" => NULL, "icon" => "fas fa-user", "color" => "text-orange", "nota" => NULL, "position" => "7", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 217, "title" => "Perfil Usuario", "default_url" => "usuario/index", "icon" => NULL, "color" => "", "nota" => NULL, "position" => "1", "parent_id" => "216", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 218, "title" => "Buscar Historial", "default_url" => "movimientos/historial", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "216", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 219, "title" => "Firma Digital", "default_url" => "firmas/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "216", "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
                ["id" => 220, "title" => "Inicio", "default_url" => "principal/index", "icon" => "fas fa-home", "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 221, "title" => "Reportar errores del sistema", "default_url" => "notificaciones/index", "icon" => "fas fa-box", "color" => "text-red", "nota" => "texto", "position" => "10", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 222, "title" => "Afiliaciones", "default_url" => NULL, "icon" => "fas fa-box", "color" => "text-green", "nota" => NULL, "position" => "3", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 223, "title" => "Afiliación empresa", "default_url" => "empresa/index", "icon" => "ni ni-bullet-list-67", "color" => "text-blue", "nota" => "texto", "position" => "1", "parent_id" => "222", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 224, "title" => "Afiliación independientes", "default_url" => "independiente/index", "icon" => "ni ni-bullet-list-67", "color" => "text-blue", "nota" => "texto", "position" => "2", "parent_id" => "222", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 225, "title" => "Afiliación pensionados", "default_url" => "pensionado/index", "icon" => "ni ni-bullet-list-67", "color" => "text-blue", "nota" => "texto", "position" => "3", "parent_id" => "222", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 226, "title" => "Afiliación facultativos", "default_url" => "facultativo/index", "icon" => "ni ni-bullet-list-67", "color" => "text-blue", "nota" => "texto", "position" => "4", "parent_id" => "222", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 227, "title" => "Cuenta usuario", "default_url" => NULL, "icon" => "fas fa-user", "color" => "text-orange", "nota" => NULL, "position" => "4", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 228, "title" => "Perfil Usuario", "default_url" => "usuario/index", "icon" => NULL, "color" => "", "nota" => NULL, "position" => "1", "parent_id" => "227", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 229, "title" => "Buscar Historial", "default_url" => "movimientos/historial", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "227", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 230, "title" => "Firma Digital", "default_url" => "firmas/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "227", "is_visible" => "1", "codapl" => "ME", "tipo" => "P"],
                ["id" => 231, "title" => "Inicio", "default_url" => "principal/index", "icon" => "fas fa-home", "color" => "text-primary", "nota" => "texto", "position" => "1", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 232, "title" => "Reportar errores del sistema", "default_url" => "notificaciones/index", "icon" => "fas fa-box", "color" => "text-red", "nota" => "texto", "position" => "10", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 233, "title" => "Foniñez", "default_url" => NULL, "icon" => "fas fa-child", "color" => "text-yellow", "nota" => NULL, "position" => "3", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 234, "title" => "Beneficiarios", "default_url" => "mercurio83/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "233", "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 235, "title" => "Eventos", "default_url" => "mercurio80/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "4", "parent_id" => "233", "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 236, "title" => "Cuenta usuario", "default_url" => NULL, "icon" => "fas fa-user", "color" => "text-orange", "nota" => NULL, "position" => "4", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 237, "title" => "Perfil Usuario", "default_url" => "usuario/index", "icon" => NULL, "color" => "", "nota" => NULL, "position" => "1", "parent_id" => "236", "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 238, "title" => "Buscar Historial", "default_url" => "movimientos/historial", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "2", "parent_id" => "236", "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 239, "title" => "Firma Digital", "default_url" => "firmas/index", "icon" => NULL, "color" => "", "nota" => "texto", "position" => "3", "parent_id" => "236", "is_visible" => "1", "codapl" => "ME", "tipo" => "F"],
                ["id" => 240, "title" => "Mi empresa", "default_url" => "empresa/miempresa", "icon" => "fas fa-building", "color" => "text-black", "nota" => "texto", "position" => "2", "parent_id" => NULL, "is_visible" => "1", "codapl" => "ME", "tipo" => "E"],
            ];

            foreach ($menuItems as $item) {
                MenuItem::create($item);
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
