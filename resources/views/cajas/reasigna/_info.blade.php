<div class='jumbotron mb-0'>
    <h5>Cambio Responsable!</h5>
    <p class='text-muted'>Esta opcion permite cambiar el responsable</p>
    <div class='lead'>
        <div class='form-group'>
            @component('components.select-field', [
                'id' => 'usuario_rea',
                'name' => 'usuario_rea',
                'options' => $data_usuarios,
                'dummy' => 'Seleccione',
                'label' => 'Lista de usuarios disponibles',
            ])@endcomponent
        </div>
        <button type='button' 
            class='btn btn-md btn-warning' 
            data-toggle='cambiar-usuario' 
            data-tipopc="{{ $tipopc }}" 
            data-id="{{ $id }}">
                Cambiar usuario responsable
        </button>
    </div>
</div>