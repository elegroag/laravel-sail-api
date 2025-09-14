@foreach ($mercurio12 as $mmercurio12)
    @php
    $mercurio13 = $Mercurio13->findFirst("tipopc='{$tipopc}' and coddoc='{$mmercurio12->getCoddoc()}' ");
    $value = "";
    if ($mercurio13 != false) $value = 'checked';
    @endphp
    <div class='col-md-6'>
        <div class='row'>
            <label class="text-primary"><?= $mmercurio12->getDetalle() ?></label>
        </div>
        <div class='row'>
            <div class='col-6 col-md-3 p-0'>Adjuntar
            </div>
            <div class='col-4 col-md-2 p-0'>
                <div class='custom-checkbox mb-3'>
                    <label class='custom-toggle'>
                        <input
                            type='checkbox'
                            id='coddoc_{{ $mmercurio12->getCoddoc() }}'
                            name='coddoc_{{ $mmercurio12->getCoddoc() }}'
                            value='{{ $mmercurio12->getCoddoc() }}'
                            {{ $value }}
                            data-toggle='archivo-guardar'
                            data-tipopc='{{ $tipopc }}'
                            data-coddoc='{{ $mmercurio12->getCoddoc() }}' />
                        <span class='custom-toggle-slider rounded-circle' data-label-off='No' data-label-on='Si'></span>
                    </label>
                </div>
            </div>

            <div class='col-6 col-md-3 pr-0'>
                {{ $value == "checked" ? 'Obliga' : '' }}
            </div>

            <div class='col-4 col-md-2 p-0'>

                @if ($value == "checked")
                    @php
                    $value = "";
                    if ($mercurio13->getObliga() == "S") $value = 'checked';
                    @endphp
                    <div class='custom-checkbox mb-3'>
                        <label class='custom-toggle'>
                            <input
                                type='checkbox'
                                id='obliga_{{ $mmercurio12->getCoddoc() }}'
                                name='obliga_{{ $mmercurio12->getCoddoc() }}'
                                value='{{ $mmercurio12->getCoddoc() }}'
                                {{ $value }}
                                data-toggle='archivo-obliga'
                                data-tipopc='{{ $tipopc }}'
                                data-coddoc='{{ $mmercurio12->getCoddoc() }}' />
                            <span class='custom-toggle-slider rounded-circle' data-label-off='No' data-label-on='Si'></span>
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach
