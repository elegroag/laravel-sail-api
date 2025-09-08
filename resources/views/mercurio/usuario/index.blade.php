@extends('layouts.bone')

@section('content')
<div id='boneLayout'></div>
@endsection

@push('scripts')
<script type="text/template" id='tmp_layout'>
    <div class="row m-2">
        <div class="col-xs-12 col-md-6">
            <div class="card">
                <div class='card-header'>
                    <h3 class="text-primary text-center">
                        <span style="margin-right:5px;line-height:1.7;"></span>
                        PERFIL DE USUARIO
                    </h3>
                </div>
                <div class="card-body px-lg-5 py-lg-3" id='app'></div>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id='tmp_perfil'>
    @include('mercurio/usuario/tmp/tmp_perfil')
</script>

<script>
    const _TITULO = "{{ $title }}";
    window.ServerController = 'usuario';
</script>

<script src="{{ asset('mercurio/build/Usuario.js') }}"></script>
@endpush
