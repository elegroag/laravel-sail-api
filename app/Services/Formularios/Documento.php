<?php

namespace App\Services\Formularios;

use App\Exceptions\DebugException;
use App\Library\Tcpdf\KumbiaPDF;
use App\Services\Srequest;

abstract class Documento
{
    protected $contenido;

    protected $filename;

    /**
     * pdf variable
     *
     * @var KumbiaPDF
     */
    protected $pdf;

    /**
     * request variable
     *
     * @var Srequest
     */
    protected $request;

    public function outPut()
    {
        $file = storage_path('temp/'.$this->filename);
        $this->pdf->Output($file, 'F');
        if (! file_exists($file)) {
            throw new DebugException('Error el documento no se genero de forma correcta', 501);
        }
        $this->pdf = null;

        return $file;
    }

    public function setParamsInit($params)
    {
        $this->request = new Srequest($params);
        $this->filename = $this->request->getParam('filename');

        $background = $this->request->getParam('background');
        if ($background) {
            KumbiaPDF::setBackgroundImage(public_path($background));
        }

        $rfirma = $this->request->getParam('rfirma');
        if ($rfirma) {
            $mfirma = $this->request->getParam('firma');
            KumbiaPDF::setFooterImage(public_path('img/firmas/'.$mfirma->getFirma()));
        }

        $this->pdf = new KumbiaPDF(null, 'P');
        $this->pdf->AddPage();
        $this->pdf->SetTextColor(1);
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetMargins(10, 10, 10);

        return $this;
    }

    public function outFile()
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename='.$this->filename.'');
        header('Cache-Control: must-revalidate');
        header('Expires: 0');
        header('Pragma: public');
        header('Content-Length: '.filesize(storage_path('temp/'.$this->filename)));
        ob_clean();
        readfile(storage_path('temp/'.$this->filename));
        exit;
    }

    protected function addBloq($datos)
    {
        foreach ($datos as $dato) {
            $this->pdf->SetXY($dato['x'], $dato['y']);
            $this->pdf->Write(0, $dato['texto']);
        }
    }

    /**
     * addBackground function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $imagen
     * @return void
     */
    protected function addBackground($imagen)
    {
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->pdf->SetHeaderMargin(0);
        $this->pdf->Image($imagen, 0, 0, 210, 279, '', '', '', false, 300, '', false, false, 0);
        $this->pdf->setPageMark();
    }

    /**
     * main function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return void
     */
    abstract public function main();
}
