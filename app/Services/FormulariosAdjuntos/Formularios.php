<?php
Core::importLibrary("FactoryDocuments", "Formularios");

class Formularios
{
    private $fabrica;

    public function __construct()
    {
        $this->fabrica = new FactoryDocuments();
    }

    private function generarDocumento($tipo, $metodo, $params)
    {
        $documento = $this->fabrica->$metodo($tipo);
        $request = $documento->setParamsInit($params);
        $documento->main($request);
        return $documento->outPut();
    }

    public function facultativoAfiliacion($params)
    {
        return $this->generarDocumento('facultativo', 'crearFormulario', $params);
    }

    public function trabajadorAfiliacion($params)
    {
        return $this->generarDocumento('trabajador', 'crearFormulario', $params);
    }

    public function empresaAfiliacion($params)
    {
        return $this->generarDocumento('empresa', 'crearFormulario', $params);
    }

    public function empresaSolicitudAfiliacion($params)
    {
        return $this->generarDocumento('empresa', 'crearOficio', $params);
    }

    public function empresaPoliticaAfiliacion($params)
    {
        return $this->generarDocumento('empresa', 'crearPolitica', $params);
    }

    public function trabajadoresNomina($params)
    {
        return $this->generarDocumento('trabajador_nomina', 'crearPolitica', $params);
    }

    public function independientesAfiliacion($params)
    {
        return $this->generarDocumento('independiente', 'crearFormulario', $params);
    }

    public function pensionadoAfiliacion($params)
    {
        return $this->generarDocumento('pensionado', 'crearFormulario', $params);
    }

    public function actualizadatosAfiliacion($params)
    {
        return $this->generarDocumento('actualizadatos', 'crearFormulario', $params);
    }
}
