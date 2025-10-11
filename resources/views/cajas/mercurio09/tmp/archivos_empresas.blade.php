@foreach ($mercurio12 as $mmercurio12)
    @php
    $mercurio14 = collect($mercurio14)->where("coddoc", $mmercurio12->getCoddoc())->first();
    $value = ($mercurio14 != false) ? 'checked' : '';
    $value2 = ($mercurio14 != false && $mercurio14->getObliga() == "S") ? 'checked' : '';
    @endphp
     <div class='row form-group'>
        <label class="text-primary col-auto"><?= $mmercurio12->getDetalle() ?></label>
          <div class='col-auto'>
            <div class='d-flex justify-content-between'>
                <label class="mr-3">Adjuntar </label>
                <div class='custom-checkbox mb-0'>
                    <label class='custom-toggle'>
                        <input
                            type='checkbox'
                            id='coddoc_{{ $mmercurio12->getCoddoc() }}'
                            name='coddoc_{{ $mmercurio12->getCoddoc() }}'
                            value='{{ $mmercurio12->getCoddoc() }}'
                            {{ $value }}
                            data-toggle='empresa-guardar'
                            data-tipopc='{{ $tipopc }}'
                            data-coddoc='{{ $mmercurio12->getCoddoc() }}' />
                        <span class='custom-toggle-slider rounded-circle' data-label-off='No' data-label-on='Si'></span>
                    </label>
                </div>
                @if ($value == "checked")
                <label class="mr-3 ml-3">Obliga </label>
                <div class='custom-checkbox mb-0'>
                    <label class='custom-toggle'>
                        <input
                            type='checkbox'
                            id='obliga_{{ $mmercurio12->getCoddoc() }}'
                            name='obliga_{{ $mmercurio12->getCoddoc() }}'
                            value='{{ $mmercurio12->getCoddoc() }}'
                            {{ $value2 }}
                            data-toggle='empresa-archivo-obliga'
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
