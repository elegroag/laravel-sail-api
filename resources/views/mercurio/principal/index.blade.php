@extends('layouts.bone')

@push('styles')
<link rel="stylesheet" href="{{ versioned_asset('mercurio/css/principal.css') }}">
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection

@push('scripts')
@include('mercurio/templates/tmp_clave_firma')

<script type="text/template" id='tmp_layout'>
    @include('mercurio/principal/tmp/tmp_layout')
</script>

<script type="text/template" id='tmp_card'>
    @include('mercurio/principal/tmp/tmp_card')
</script>

<script type="text/template" id='tmp_totales'>
    @include('mercurio/principal/tmp/tmp_totales')
</script>

<script type="text/template" id='tmp_galeria_carousel'>
    @include('mercurio/principal/tmp/tmp_galeria_carousel')
</script>
<script src="{{ versioned_asset('mercurio/build/Principal.js') }}"></script>

{{-- Mensaje informativo en la sección "Consultas" cuando el usuario está inactivo.
     Lee el flag `consultas_habilitadas` devuelto por /principal/servicios; si es false,
     muestra #show_consultas_message y oculta el grid de tarjetas. --}}
<script>
    (function () {
        const url = @json(route('principal.servicios'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function showInactiveMessage() {
            const grid = document.getElementById('show_consultas');
            const message = document.getElementById('show_consultas_message');
            if (grid) {
                // Limpia cualquier tarjeta que Principal.js hubiera podido inyectar
                // antes de que esta respuesta llegara, y oculta el grid.
                grid.innerHTML = '';
                grid.classList.add('d-none');
            }
            if (message) message.classList.remove('d-none');
        }

        function hideInactiveMessage() {
            const grid = document.getElementById('show_consultas');
            const message = document.getElementById('show_consultas_message');
            if (message) message.classList.add('d-none');
            if (grid) grid.classList.remove('d-none');
        }

        // Espera a que Principal.js termine de pintar las tarjetas y rellene #show_consultas.
        // Si después de la carga el grid quedó vacío y consultas_habilitadas=false,
        // mostramos el mensaje.
        function applyState(consultasHabilitadas, hasCards) {
            if (!consultasHabilitadas || !hasCards) {
                showInactiveMessage();
            } else {
                hideInactiveMessage();
            }
        }

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
            .then((r) => (r.ok ? r.json() : null))
            .then((json) => {
                if (!json || json.success !== true) return;
                const habilitadas = json.consultas_habilitadas === true;
                const cards = Array.isArray(json.data?.consultas) ? json.data.consultas : [];
                applyState(habilitadas, cards.length > 0);
            })
            .catch(() => {
                // Silencioso: si falla el endpoint, dejamos la UI por defecto.
            });
    })();
</script>
@endpush