<?php

namespace App\Services\Utils;

use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio37;
use Illuminate\Support\Facades\DB;

class RegistroSeguimiento
{
    /**
     * crearNota function
     * Se crea la nota de seguimeinto
     *
     * @param [type] $tipopc
     * @param [type] $id
     * @param [type] $nota
     * @param  string  $estado
     * @return void
     */
    public function crearNota($tipopc, $id, $nota, $estado)
    {
        $item = Mercurio10::where([
            'tipopc' => $tipopc,
            'numero' => $id,
        ])->max('item') + 1;

        $mercurio10 = new Mercurio10;
        $mercurio10->setTipopc($tipopc);
        $mercurio10->setNumero($id);
        $mercurio10->setItem($item);
        $mercurio10->setEstado($estado);
        $mercurio10->setNota($nota);
        $mercurio10->setFecsis(date('Y-m-d'));
        $mercurio10->save();
    }

    /**
     * consultaSeguimiento function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  int  $tipopc
     * @param  object  $mercurio
     * @return void
     */
    public function consultaSeguimiento($tipopc, $mercurio)
    {
        $mercurio10 = Mercurio10::where('tipopc', $tipopc)
            ->where('numero', $mercurio->getId())
            ->orderBy('item', 'asc');

        $table = new Table;
        $table->set_template($this->getTemplateTable());
        $table->set_heading(
            'Observaciones',
            'Estado',
            'Fecha del seguimiento'
        );

        if ($mercurio10->count() == 0) {
            $table->add_row('No hay datos de seguimiento', '');
        } else {
            foreach ($mercurio10->get() as $mmercurio10) {
                $nota = strip_tags(strtolower($mmercurio10->getNota()));
                $table->add_row(
                    $nota,
                    $mmercurio10->getDetalleEstado(),
                    $mmercurio10->getFecsis()
                );
            }
        }

        return $table->generate();
    }

    /**
     * loadAdjuntos function
     *
     * @changed [2023-12-27]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  int  $tipopc
     * @param  object  $mercurio30
     * @return string
     */
    public function loadAdjuntos($tipopc, $mercurio30)
    {
        $id = $mercurio30->getId();
        $mercurio37 = Mercurio37::select([
            DB::raw('mercurio37.*'),
            'mercurio12.detalle',
        ])
            ->where([
                'tipopc' => $tipopc,
                'numero' => $mercurio30->getId(),
            ])
            ->join('mercurio12', 'mercurio37.coddoc', '=', 'mercurio12.coddoc')
            ->get();

        return view('partials.adjuntos', compact('mercurio37', 'id'))->render();;
    }

    public function getTemplateTable()
    {
        return [
            'table_open' => '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">',
            'thead_open' => '<thead class="thead-light">',
            'thead_close' => '</thead>',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th style="font-size:0.81rem">',
            'heading_cell_end' => '</th>',
            'tbody_open' => '<tbody class="list">',
            'tbody_close' => '</tbody>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td style="white-space:initial;font-size:0.95rem">',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>',
        ];
    }
}
