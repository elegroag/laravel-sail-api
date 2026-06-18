<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio09;
use App\Services\Reportes\ReporteSolicitudes;
use App\Services\Reportes\SolicitudesExcelExporter;
use App\Services\Srequest;
use App\Services\Utils\Pagination;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesolController extends ApplicationController
{
    /**
     * pagination variable
     *
     * @var Pagination
     */
    protected $pagination;

    protected ?DbBase $db;

    protected ?array $user;

    protected ?string $tipo;

    public function __construct()
    {
        $this->pagination = new Pagination;
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipfun') ?? null;
    }

    public function index()
    {
        $m09 = (new Mercurio09)->get();
        $tipo_solicitudes = [];
        foreach ($m09 as $model09) {
            $tipo_solicitudes[$model09->getTipopc()] = $model09->getDetalle();
        }
        return view('cajas.reportesol.index', [
            'title' => 'Reportes de Solicitudes',
            'tipo_solicitudes' => $tipo_solicitudes,
        ]);
    }

    public function procesar(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:1,2,3,4',
            'estado' => 'nullable|string|max:5',
            'fecha_solicitud' => 'nullable|date_format:Y-m-d',
            'fecha_aprueba' => 'nullable|date_format:Y-m-d',
        ]);

        $reporte = new ReporteSolicitudes;
        $dataset = $reporte->buildDataset(new Srequest($validated));

        if ($dataset === null) {
            abort(400, 'Tipo de solicitud no soportado');
        }

        $filename = sprintf(
            'reporte_solicitudes_%s_%s.xlsx',
            $validated['tipo'],
            now()->format('Ymd_His')
        );

        return SolicitudesExcelExporter::stream($dataset, $filename);
    }
}
