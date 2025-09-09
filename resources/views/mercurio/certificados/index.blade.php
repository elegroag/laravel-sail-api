@php
    use App\Services\Tag;
    use App\Services\Request;
@endphp
@extends('layouts.bone')

@section('content')
<div class="header bg-gradient-primary pb-9">
    <div class="container-fluid">
        <div class="header-body p-4">
            <div id='header_group_button'>
                <div class="row justify-content-start">
                    <div class="col-xs-12 col-auto">
                        <h4 class="text-white d-inline-block mb-0">Presentar Certificados</h4>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><span class="text-white"><i class="fas fa-box"></i></span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class='card-header bg-green-blue p-1' id='render_subeader'></div>
                <div class="card-body m-3">
                    @if($certificadosPresentados)
                        <div id='consulta' class='card-body'>
                            <h4 class="text-primary">Certificados Presentados</h4>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:10%">Código</th>
                                        <th>Nombre beneficiario</th>
                                        <th>Nombre certificado</th>
                                        <th style="width:10%">Fecha solicitud</th>
                                        <th style="width:10%">Estado solicitud</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificadosPresentados as $certPresentado)
                                        <tr>
                                            <td>{{ $certPresentado->getCodben() }}</td>
                                            <td>
                                                <p style="font-size: .92rem;">{{ Tag::capitalize($certPresentado->getNombre()) }}</p>
                                            </td>
                                            <td>{{ capitalize($certPresentado->getNomcer()) }}</td>
                                            <td>{{ $certPresentado->getFecha() }}</td>
                                            <td>{{ capitalize($certPresentado->getEstadoDetalle()) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(!$subsi22)
                        <div id='consulta' class='card-body'>
                            <p style="font-size: 1rem;">
                                No dispone de beneficiarios pendientes por cargue de certificados.
                            </p>
                        </div>
                    @else
                        <div id='consulta' class='card-body'>
                            <h4 class="text-primary">Beneficiarios</h4>
                            @foreach($subsi22 as $beneficiarioCerti)
                                @php
                                    $certDisponibles = array();
                                    if (!empty($beneficiarioCerti['certificadoPendiente'])) {
                                        foreach ($beneficiarioCerti['codcer'] as $ai => $value) {
                                            $has = 0;
                                            foreach ($beneficiarioCerti['certificados'] as $certificado) {
                                                if ($ai == $certificado->getCodcer()) {
                                                    $has++;
                                                    break;
                                                }
                                            }
                                            if ($has == 0) {
                                                $certDisponibles[$ai] = $value;
                                            }
                                        }
                                    } else {
                                        $certDisponibles = $beneficiarioCerti['codcer'];
                                    }
                                @endphp
                                <div class='row'>
                                    <div class='col ml-auto'>
                                        <p style="font-size: 0.92rem;">
                                            Nombre: {{ capitalize($beneficiarioCerti['nombre']) }}<br />
                                            {{ (count($certDisponibles) == 0) ? 'Los certificados estan pendiente de validación' : $beneficiarioCerti['ultfec'] }}
                                        </p>
                                    </div>
                                </div>
                                @if(count($certDisponibles) > 0)
                                    <div class='row'>
                                        <div class='col-md-5 ml-auto'>
                                            @php echo Tag::selectStatic(new Request(
                                                [
                                                   "name"=> "codcer_" . $beneficiarioCerti['codben'], 
                                                   "options"=> $certDisponibles, 
                                                   "use_dummy"=> true,
                                                   "class"=> "form-control"
                                                ]
                                            )); @endphp
                                        </div>
                                        <div class='col-md-4'>
                                            <div class='custom-file'>
                                                <input type='file' class='custom-file-input' 
                                                    id='archivo_{{ $beneficiarioCerti['codben'] }}' 
                                                    name='archivo_{{ $beneficiarioCerti['codben'] }}' 
                                                    accept='application/pdf, image/*'>
                                                
                                                <label 
                                                    style="font-size: 0.8rem;" 
                                                    class='custom-file-label' 
                                                    for='customFileLang'>Selecionar documento aquí...</label>
                                            </div>
                                        </div>
                                        <div class='col-md-auto mr-auto'>
                                            <button 
                                                class='btn btn-icon btn-primary' 
                                                type='button' 
                                                id="btnSalvarCertificado" 
                                                data-codben="{{ $beneficiarioCerti['codben'] }}"    
                                                data-nombre="{{ $beneficiarioCerti['nombre'] }}">
                                                    <span class='btn-inner--icon'><i class='fas fa-plus'></i></span>
                                                    <span class='btn-inner--text'>Adjuntar</span>
                                            </button>
                                        </div>
                                    </div>
                                    <hr />
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        const _TITULO = "{{ $title }}";
        window.ServerController = 'certificados';
    </script>

    <script src="{{ asset('core/upload.js') }}"></script>
    <script src="{{ asset('mercurio/build/Certificados.js') }}"></script>
@endpush
