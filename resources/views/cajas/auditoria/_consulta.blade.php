<div class='table-responsive'>
    <table class='table'>
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
            @foreach ($mercurio['datos'] as $mmercurio)
            @if ($tipopc == 1 || $tipopc == 9 || $tipopc == 10 || $tipopc == 11 || $tipopc == 12)
                @php
                $documento = 'getCedtra';
                $nombre = 'getNombre';
                @endphp
            @endif
            @if ($tipopc == 2)
                @php
                $documento = 'getNit';
                $nombre = 'getRazsoc';
                @endphp
            @endif
            @if ($tipopc == 3)
                @php
                $documento = 'getCedcon';
                $nombre = 'getNombre';
                @endphp
            @endif
            @if ($tipopc == 4)
                @php
                $documento = 'getNumdoc';
                $nombre = 'getNombre';
                @endphp
            @endif
            @if ($tipopc == 5)
                @php
                $documento = 'getDocumento';
                $nombre = 'getDocumentoDetalle';
                $extra = $mmercurio->getCampoDetalle().' - '.$mmercurio->getAntval().' - '.$mmercurio->getValor();
                @endphp
            @endif
            @if ($tipopc == 7)
                @php
                $documento = 'getCedtra';
                $nombre = 'getNomtra';
                @endphp
            @endif
            @if ($tipopc == 8)
                @php
                $documento = 'getCodben';
                $nombre = 'getNombre';
                $extra = $mmercurio->getNomcer();
                @endphp
            @endif
        @php

        $gener02 = $this->Gener02->findFirst("usuario = '{$mmercurio->getUsuario()}'");
        if ($gener02 == false) {
            $gener02 = new Gener02;
        }
        $mercurio20 = $this->Mercurio20->findFirst("log = '{$mmercurio->getLog()}'");
        if ($mercurio20 == false) {
            $mercurio20 = new Mercurio20;
        }
        @endphp
        @php
        $dias_vencidos = CalculatorDias::calcular($tipopc, $mmercurio->getId());
        @endphp
        <tr>
            <td>{{$mmercurio->$documento()}}</td>
            <td>{{$mmercurio->$nombre()}}</td>
            <td>{{$gener02->getNombre()}}</td>
            <td>{{$mmercurio->getFecest()->getUsingFormatDefault()}}</td>
            <td>{{$dias_vencidos}}</td>
            @if ($tipopc == '8' || $tipopc == '5')
            <td>{{$extra}}</td>
            @endif
            <td>{{$mmercurio->getEstadoDetalle()}}</td>
            <td class='table-actions'>
                <a href='#!'
                    class='table-action btn btn-xs btn-primary'
                    title='Info'
                    onclick="info('$tipopc','{$mmercurio->getId()}')">
                    <i class='fas fa-info'></i>
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
