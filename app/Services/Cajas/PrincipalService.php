<?php

namespace App\Services\Cajas;

use App\Models\Gener02;
use App\Models\Mercurio06;
use App\Models\Mercurio07;
use App\Models\Mercurio08;
use App\Models\Mercurio09;
use App\Models\Mercurio10;
use App\Models\Mercurio20;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio33;
use App\Models\Mercurio34;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Models\Mercurio47;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrincipalService
{
    /**
     * @return array{
     *   afiliacion: list<array{name: string, cantidad: array{pendientes: int, aprobados: int, rechazados: int, devueltos: int, temporales: int}, icon: string, url: string, href: string, imagen: string, imagen_url: string}>,
     *   productos: list<array{name: string, cantidad: int, icon: string, url: string, href: string, imagen: string, imagen_url: string}>
     * }
     */
    public function buildServicios(int|string $usuario): array
    {
        return [
            'afiliacion' => [
                $this->tarjetaAfiliacion('Afiliación Empresas', Mercurio30::query(), $usuario, 'E', 'aprobacionemp/index', 'empresas.jpg'),
                $this->tarjetaAfiliacion('Afiliación Independientes', Mercurio41::query(), $usuario, 'I', 'aprobaindepen/index', 'independiente.jpg'),
                $this->tarjetaAfiliacion('Afiliación Pensionados', Mercurio38::query(), $usuario, 'P', 'aprobacionpen/index', 'pensionado.jpg'),
                $this->tarjetaAfiliacion('Afiliación Trabajadores', Mercurio31::query(), $usuario, 'T', 'aprobaciontra/index', 'trabajadores.jpg'),
                $this->tarjetaAfiliacion('Afiliación Conyuges', Mercurio32::query(), $usuario, 'C', 'aprobacioncon/index', 'conyuges.jpg'),
                $this->tarjetaAfiliacion('Afiliación Beneficiarios', Mercurio34::query(), $usuario, 'B', 'aprobacionben/index', 'beneficiarios.jpg'),
                $this->tarjetaAfiliacion('Actualización datos Empresas', Mercurio33::query(), $usuario, 'AE', 'actualizaemp/index', 'empresas.jpg'),
                $this->tarjetaAfiliacion('Actualización datos Trabajador', Mercurio47::query(), $usuario, 'AT', 'actualizatra/index', 'datos_basicos.jpg'),
            ],
            'productos' => [
                $this->tarjetaProducto('Lista Productos', 'L', 'admproductos/lista', 'registro_empresa.jpg'),
                $this->tarjetaProducto('Complemento nutricional', 'N', 'admproductos/aplicados/27', 'complemento.jpg'),
            ],
        ];
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array{name: string, cantidad: array{pendientes: int, aprobados: int, rechazados: int, devueltos: int, temporales: int}, icon: string, url: string, href: string, imagen: string, imagen_url: string}
     */
    private function tarjetaAfiliacion(
        string $name,
        Builder $query,
        int|string $usuario,
        string $icon,
        string $url,
        string $imagen
    ): array {
        $base = (clone $query)->where('usuario', $usuario);

        return [
            'name' => $name,
            'cantidad' => [
                'pendientes' => (clone $base)->where('estado', 'P')->count(),
                'aprobados' => (clone $base)->where('estado', 'A')->count(),
                'rechazados' => (clone $base)->where('estado', 'R')->count(),
                'devueltos' => (clone $base)->where('estado', 'D')->count(),
                'temporales' => (clone $base)->where('estado', 'T')->count(),
            ],
            'icon' => $icon,
            'url' => $url,
            'href' => '/cajas/'.$url,
            'imagen' => $imagen,
            'imagen_url' => asset('img/Mercurio/'.$imagen),
        ];
    }

    /**
     * @return array{name: string, cantidad: int, icon: string, url: string, href: string, imagen: string, imagen_url: string}
     */
    private function tarjetaProducto(string $name, string $icon, string $url, string $imagen): array
    {
        return [
            'name' => $name,
            'cantidad' => 0,
            'icon' => $icon,
            'url' => $url,
            'href' => '/cajas/'.$url,
            'imagen' => $imagen,
            'imagen_url' => asset('img/Mercurio/'.$imagen),
        ];
    }

    /**
     * @return array{
     *   usuarios: array{data: list<int>, labels: list<string>},
     *   opcion: array{data: list<int>, labels: list<string>},
     *   rechazo: array{data: list<int>, labels: list<string>},
     *   laboral: array{data: list<int>, labels: list<string>}
     * }
     */
    public function buildDashboardCharts(): array
    {
        return [
            'usuarios' => $this->usuariosRegistrados(),
            'opcion' => $this->opcionMasUsada(),
            'rechazo' => $this->motivoMasUsado(),
            'laboral' => $this->cargaLaboral(),
        ];
    }

    /**
     * @return array{data: list<int>, labels: list<string>}
     */
    public function usuariosRegistrados(): array
    {
        $data = [];
        $labels = [];

        $rows = Mercurio07::query()
            ->select('tipo', DB::raw('count(*) as documento'))
            ->groupBy('tipo')
            ->get();

        $detalles = Mercurio06::query()
            ->whereIn('tipo', $rows->pluck('tipo')->filter()->all())
            ->pluck('detalle', 'tipo');

        foreach ($rows as $row) {
            $data[] = (int) $row->documento;
            $labels[] = (string) ($detalles[$row->tipo] ?? $row->tipo);
        }

        return ['data' => $data, 'labels' => $labels];
    }

    /**
     * @return array{data: list<int>, labels: list<string>}
     */
    public function opcionMasUsada(): array
    {
        $data = [];
        $labels = [];

        $rows = Mercurio20::query()
            ->select('accion', DB::raw('count(*) as log'))
            ->groupBy('accion')
            ->get();

        foreach ($rows as $row) {
            $data[] = (int) $row->log;
            $labels[] = (string) $row->accion;
        }

        return ['data' => $data, 'labels' => $labels];
    }

    /**
     * @return array{data: list<int>, labels: list<string>}
     */
    public function motivoMasUsado(): array
    {
        $data = [];
        $labels = [];

        $rows = Mercurio10::query()
            ->select('mercurio11.detalle', 'mercurio10.codest', DB::raw('count(*) as cantidad'))
            ->join('mercurio11', 'mercurio10.codest', '=', 'mercurio11.codest')
            ->groupBy('mercurio10.codest', 'mercurio11.detalle')
            ->get();

        foreach ($rows as $row) {
            $data[] = (int) $row->cantidad;
            $labels[] = (string) $row->detalle;
        }

        return ['data' => $data, 'labels' => $labels];
    }

    /**
     * @return array{data: list<int>, labels: list<string>}
     */
    public function cargaLaboral(): array
    {
        $tipopcs = Mercurio09::query()->pluck('tipopc')->filter()->unique()->values()->all();

        if ($tipopcs === []) {
            return ['data' => [], 'labels' => []];
        }

        $counts = Mercurio08::query()
            ->select('usuario', DB::raw('count(*) as total'))
            ->whereIn('tipopc', $tipopcs)
            ->groupBy('usuario')
            ->pluck('total', 'usuario');

        $usuarios = Gener02::query()
            ->select('gener02.usuario', 'gener02.nombre')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->distinct()
            ->orderBy('gener02.nombre')
            ->get();

        $data = [];
        $labels = [];

        foreach ($usuarios as $usuario) {
            $data[] = (int) ($counts[$usuario->usuario] ?? 0);
            $labels[] = (string) $usuario->nombre;
        }

        return ['data' => $data, 'labels' => $labels];
    }
}
