<?php

namespace App\Services\Formularios\Oficios;

use App\Exceptions\DebugException;
use App\Services\Formularios\Documento;
use App\Services\Utils\Table;

class TrabajadoresNominaEmpresa extends Documento
{
    private $tranoms;

    private $empresa;

    /**
     * main function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  TCPDF  $pdf
     * @param [type] $params
     * @return void
     */
    public function main()
    {
        if (! $this->request->getParam('empresa')) {
            throw new DebugException('Error la empresa no esté disponible', 501);
        }

        $this->empresa = $this->request->getParam('empresa');
        $this->tranoms = $this->request->getParam('tranoms');

        $this->pdf->SetTitle("Trabajadores en nomina con NIT {$this->empresa->getNit()}, COMFACA");
        $this->pdf->SetAuthor("{$this->empresa->getPriape()} {$this->empresa->getSegape()} {$this->empresa->getPrinom()} {$this->empresa->getSegnom()}, COMFACA");
        $this->pdf->SetSubject('Trabajadores en nomina');
        $this->pdf->SetCreator('Plataforma Web: comfacaenlinea.com.co, COMFACA');
        $this->pdf->SetKeywords('COMFACA');

        $this->bloqueEmpresa();
        $selloFirma = public_path('img/firmas/sello-firma.png');
        $this->pdf->Image($selloFirma, 160, 265, 30, 20, '', '', '', false, 300, '', false, false, 0);
    }

    /**
     * bloqueEmpresa function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param  Mercurio30  $empresa
     * @param  TCPDF  $pdf
     * @return void
     */
    public function bloqueEmpresa()
    {
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('helvetica', 'B', 15);
        $this->pdf->Write(0, 'Relación Trabajadores En Nomina', '', 0, 'C', true, 0, false, false, 0);

        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Write(0, 'Señores', '', 0, 'L', true, 0, false, false, 0);
        $this->pdf->Write(0, 'Caja de Compensación Familiar del Caquetá COMAFCA', '', 0, 'L', true, 0, false, false, 0);
        $this->pdf->Write(0, 'Cra. 11 N° 10 - 34', '', 0, 'L', true, 0, false, false, 0);
        $this->pdf->Write(0, 'Florencia Caquetá', '', 0, 'L', true, 0, false, false, 0);

        $this->pdf->Ln();

        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Write(0, 'Yo '.capitalize($this->empresa->getRepleg()).'. Identificado con '.
            $this->empresa->getCoddocrepleg().' número '.$this->empresa->getCedrep().'. Representante Legal de la empresa '.capitalize($this->empresa->getRazsoc()).
            '. Con NIT '.$this->empresa->getNit().
            '. Certifico que las personas relacionadas en este documento figuran en nomina y laboran en el Departamento del Caquetá.', '', 0, 'L', true, 0, false, false, 0);
        $this->pdf->Ln();
        $this->pdf->Ln();

        $tbl = new Table;
        $tbl->set_template([
            'table_open' => '<table border="1" cellpadding="3" cellspacing="0">',
            'thead_open' => '<thead>',
            'thead_close' => '</thead>',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'tbody_open' => '<tbody>',
            'tbody_close' => '</tbody>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>',
        ]);

        $tbl->set_heading([
            ['data' => 'ID', 'width' => '30mm'],
            ['data' => 'Nombre', 'width' => '40mm'],
            ['data' => 'Salario', 'width' => '30mm'],
            ['data' => 'Fecha inicia', 'width' => '20mm'],
            ['data' => 'Cargo', 'width' => '70mm'],
        ]);

        foreach ($this->tranoms as $tranom) {
            $tbl->add_row([
                ['data' => $tranom->getCedtra(), 'width' => '30mm'],
                ['data' => capitalize($tranom->getNomtra().' '.$tranom->getApetra()), 'width' => '40mm'],
                ['data' => '$ '.number_format($tranom->getSaltra(), 0, '.', '.'), 'width' => '30mm'],
                ['data' => $tranom->getFectra(), 'width' => '20mm'],
                ['data' => $tranom->getCartra(), 'width' => '70mm'],
            ]);
        }
        $html = $tbl->generate();
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->writeHTML($html, true, false, false, false, '');

        return $this->pdf;
    }
}
