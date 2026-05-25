<div class='table-responsive mt-2'>
    <table class='table table-striped table-bordered'>
        <tr>
            <td>Documento</td>
            <td>Nombre</td>
            <td>Responsable</td>
            <td>Fecha</td>
            <td>Dias</td>
            @if ($tipopc == '8' || $tipopc == '5')
                <td></td>
            @endif
            <td>Estado</td>
        </tr>
@php
use App\Models\Gener02;
use App\Models\Mercurio20;
use App\Services\Utils\CalculatorDias;
@endphp
        @foreach ($mercurio['datos'] as $mmercurio)
            @if ($tipopc == 1 || $tipopc == 9 || $tipopc == 10)
                @php
                $documento = 'getCedtra';
                $nombre = 'getNombre';
                @endphp
            @elseif ($tipopc == 11 || $tipopc == 12)
                @php
                $documento = 'getCedtra';
                $nombre = function($m) { return trim($m->getPrinom().' '.$m->getSegnom().' '.$m->getPriape().' '.$m->getSegape()); };
                @endphp
            @elseif ($tipopc == 2)
                @php
                $documento = 'getNit';
                $nombre = 'getRazsoc';
                @endphp
            @elseif ($tipopc == 3)
                @php
                $documento = 'getCedcon';
                $nombre = function($m) { return trim($m->getPrinom().' '.$m->getSegnom().' '.$m->getPriape().' '.$m->getSegape()); };
                @endphp
            @elseif ($tipopc == 4)
                @php
                $documento = 'getNumdoc';
                $nombre = function($m) { return trim($m->getPrinom().' '.$m->getSegnom().' '.$m->getPriape().' '.$m->getSegape()); };
                @endphp
            @elseif ($tipopc == 5)
                @php
                $documento = 'getDocumento';
                $nombre = 'getDocumentoDetalle';
                $extra = $mmercurio->getCampoDetalle().' - '.$mmercurio->getAntval().' - '.$mmercurio->getValor();
                @endphp
            @elseif ($tipopc == 7)
                @php
                $documento = 'getCedtra';
                $nombre = 'getNomtra';
                @endphp
            @elseif ($tipopc == 8)
                @php
                $documento = 'getCodben';
                $nombre = 'getNombre';
                $extra = $mmercurio->getNomcer();
                @endphp
            @endif
            @php
            $gener02 = $gener02Map[$mmercurio->getUsuario()] ?? null;
            if ($gener02 == null) {
                $gener02 = new Gener02;
            }
            $mercurio20 = $mercurio20Map[$mmercurio->getLog()] ?? null;
            if ($mercurio20 == null) {
                $mercurio20 = new Mercurio20;
            }
            $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());
            @endphp
        <tr>
            <td>{{$mmercurio->$documento()}}</td>
            <td>{{ is_callable($nombre) ? $nombre($mmercurio) : $mmercurio->$nombre() }}</td>
            <td>{{$gener02->getNombre()}}</td>
            <td>{{$mmercurio->getFecest()}}</td>
            <td>{{$dias_vencidos}}</td>
            @if ($tipopc == '8' || $tipopc == '5')
            <td>{{$extra}}</td>
            @endif
            <td>{{$mmercurio->getEstadoDetalle()}}</td>
        </tr>
        @endforeach
    </table>
</div>
