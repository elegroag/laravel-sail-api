@php
$id = $mercurio30->getId();
@endphp

<script id='tmp_card_header' type="text/template">
    <div id="botones" class='row justify-content-end'>
        <% if(empresa != 0){ %>
            <a href="<%=url%>" class='btn btn-sm btn-success'><i class=''></i> Empresa Sisuweb</a>&nbsp;
        <% } %>
        <a href="#" data-href="aprobacionemp/editar_ficha/<?= $id ?>" class='btn btn-sm btn-warning' id='editar_ficha'><i class=''></i> Editar Ficha Empleador</a>&nbsp;
        <a href="#" data-href="aprobacionemp/index" class='btn btn-sm btn-primary' id='cancelar_volver'><i class='fas fa-hand-point-up text-white'></i> Salir</a>&nbsp;
    </div>
</script>

<div class='card-header pt-2 pb-2' id='afiliacion_header'></div>

<div class='card-body'>
    @php echo $consulta_empresa; @endphp
    <hr class='my-3'>
    <h6 class='heading-small text-muted mb-2'>Acciones </h6>
    <div class='row'>
        <div class='col-md-12'>
            <div class='nav-wrapper'>
                <ul class='nav nav-pills' id='v-pills-tab' role='tablist'>
                    <li class='nav-item'>
                        <a class='nav-link active' id='v-pills-home-tab' data-bs-toggle='pill' href='#v-aprobar' role='tab'>Aprobar</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='v-pills-profile-tab' data-bs-toggle='pill' href='#v-devolver' role='tab'>Devolver</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link' id='v-pills-messages-tab' data-bs-toggle='pill' href='#v-rechazar' role='tab'>Rechazar</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class='col-md-12'>
            <div class='tab-content' id='v-pills-tabContent'>
                <div class='tab-pane fade show active' id='v-aprobar' role='tabpanel' aria-labelledby='v-pills-home-tab'>
                    <div class='card'>
                        <div class='col-xs-12'>
                            <div class='jumbotron mb-1 py-2'>
                                <form method="POST" action="#!" id='form_aprobar'>
                                    <h2>Aprobar</h2>
                                    <p>Esta opcion es para aprobar la empresa y enviar los datos a Subsidio</p>
                                    <hr class='my-3'>
                                    <div class='row'>
                                        <div class='col-md-2'>
                                            <div class='form-group'>
                                                <label for='tipdur' class='form-control-label'>Duración</label>
                                                @php echo Tag::selectStatic("tipdur", $_tipdur, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-4'>
                                            <div class='form-group'>
                                                <label for='codind' class='form-control-label'>Indice</label>
                                                @php echo Tag::selectStatic("codind", $_codind, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-2'>
                                            <div class='form-group'>
                                                <label for='todmes' class='form-control-label'>Paga mes</label>
                                                @php echo Tag::selectStatic("todmes", $_todmes, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-4'>
                                            <div class='form-group'>
                                                <label for='forpre' class='form-control-label'>Forma presentación</label>
                                                @php echo Tag::selectStatic("forpre", $_forpre, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='col-md-2'>
                                            <div class='form-group'>
                                                <label for='pymes' class='form-control-label'>Pyme</label>
                                                @php echo Tag::selectStatic("pymes", $_pymes, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='contratista' class='form-control-label'>Contratista</label>
                                                @php echo Tag::selectStatic("contratista", $_contratista, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='tipemp' class='form-control-label'>Tipo Empresa</label>
                                                @php echo Tag::selectStatic("tipemp", $_tipemp, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-4'>
                                            <div class='form-group'>
                                                <label for='tipapo' class='form-control-label'>Tipo Aportante </label>
                                                @php echo Tag::selectStatic("tipapo", $_tipapo, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='tipsoc' class='form-control-label'>Tipo Sociedad</label>
                                                @php echo Tag::selectStatic("tipsoc", $_tipsoc, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-4'>
                                            <div class='form-group'>
                                                <label for='ofiafi' class='form-control-label'>Oficina</label>
                                                @php echo Tag::selectStatic("ofiafi", $_ofiafi, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-2'>
                                            <div class='form-group'>
                                                <label for='colegio' class='form-control-label'>Colegio</label>
                                                @php echo Tag::selectStatic("colegio", $_colegio, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='tipdoc' class='form-control-label'>Fecha Afiliación</label>
                                                @php echo Tag::calendar("fecafi", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='subpla' class='form-control-label'>Sucursal planilla</label>
                                                @php echo Tag::textField("subpla", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='actapr' class='form-control-label'>Acta Aprobación</label>
                                                @php echo Tag::textField("actapr", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='diahab' class='form-control-label'>Día habil de Pago </label>
                                                @php echo Tag::textField("diahab", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                        <div class='col-md-3'>
                                            <div class='form-group'>
                                                <label for='feccap' class='form-control-label'>Fecha Resolución</label>
                                                @php echo Tag::calendar("feccap", "class: form-control"); @endphp
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class='row'>
                                    <div class='col-md-8'>
                                        <div class='form-group'>
                                            <label for='nota' class='form-control-label'>Nota</label>
                                            <textarea class='form-control summer_content' id='nota_aprobar' rows='3'></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="box form-group pt-3">
                                    <button type='button' class='btn btn-md btn-success' style='width:200px' id='aprobar_solicitud'>Aprobar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='tab-pane fade' id='v-devolver' role='tabpanel' aria-labelledby='v-pills-profile-tab'>
                    <div class='row'>
                        <div class='col'>
                            <div class='jumbotron mb-1 py-4'>
                                <h2>Devolver</h2>
                                <p>Esta opcion es para rechazar a la empresa e informarle la causal del rechazo</p>
                                <hr class='my-3'>
                                <div class="col-md-12">
                                    <div class='form-group'>
                                        <label class='label'> Motivo:</label>
                                        @php echo Tag::selectStatic("codest_devolver", $mercurio11, "use_dummy: true", "dummyValue: ", "class: form-control"); @endphp
                                    </div>
                                    <div class='form-group'>
                                        <label class='label'> Campos para corregir:</label>
                                        <select class="js-basic-multiple" name="campos_corregir[]" id='campos_corregir' multiple="multiple">
                                            @foreach ($mercurio30->CamposDisponibles() as $kei => $campos) {
                                                @php
                                                echo Tag::selectStatic("campos_corregir", $campos, "use_dummy: true", "dummyValue: ", "class: form-control");
                                                @endphp
                                            }
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class='col-md-8'>
                                    <div class='form-group'>
                                        <textarea class='form-control summer_content' id='nota_devolver' rows='3'></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class='form-group'>
                                        <button type='button' class='btn btn-md btn-warning' style='width:300px' onclick='devolver_registro(<?= $id ?>)'>Devolver</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='tab-pane fade' id='v-rechazar' role='tabpanel' aria-labelledby='v-pills-messages-tab'>
                    <div class='row'>
                        <div class='col'>
                            <div class='jumbotron mb-1 py-4'>
                                <h2>Rechazar</h2>
                                <p>Esta opcion es para rechazar a la empresa e informarle la causal del rechazo</p>
                                <hr class='my-3'>
                                <div class="col-md-12">
                                    <div class='form-group'>
                                        <?= Tag::select("codest", $mercurio11, "using: codest,detalle", "use_dummy: true", "dummyValue: ", "class: form-control"); ?>
                                    </div>
                                    <div class='form-group'>
                                        <textarea class='form-control summer_content' id='nota_rechazar' rows='4'></textarea>
                                    </div>
                                    <div class='form-group'>
                                        <button type='button' class='btn btn-md btn-danger' style='width:300px' onclick='rechazar_registro(<?= $id ?>)'>Rechazar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const init_header = function(_empresa = 0) {
        let _template = _.template($("#tmp_card_header").html());
        let _url = '';
        if (_empresa != 0) {
            _url = Utils.getKumbiaURL('aprobacionemp/empresa_sisuweb/' + _empresa.nit)
        }
        $(".card-header").html(_template({
            empresa: _empresa,
            url: _url
        }));

        $('#cancelar_volver').click(function(event) {
            event.preventDefault();
            let _target = $(event.currentTarget);
            window.location = Utils.getKumbiaURL(_target.attr('data-href'));
        });

        $("#editar_ficha").click(function(event) {
            event.preventDefault();
            let _target = $(event.currentTarget);
            window.location = Utils.getKumbiaURL(_target.attr('data-href'));
        });
    };

    $(document).ready(function() {
        <? if (isset($empresa_sisuweb)) { ?>
            let _empresa = <?= json_encode($empresa_sisuweb) ?>;
            swal.fire({
                "title": "Notificación",
                "text": "La empresa " + _empresa.razsoc + " ya se encuentra registrada en SisuWeb.",
                "icon": "warning",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 20000
            });
            init_header(_empresa);
        <? } else { ?>
            init_header(0);
        <? } ?>

        $("#form_aprobar").validate({
            rules: {
                tipdur: {
                    required: true
                },
                actapr: {
                    required: true
                },
                codind: {
                    required: true
                },
                todmes: {
                    required: true
                },
                forpre: {
                    required: true
                },
                pymes: {
                    required: true
                },
                contratista: {
                    required: true
                },
                tipemp: {
                    required: true
                },
                tipsoc: {
                    required: true
                },
                tipapo: {
                    required: true
                },
                ofiafi: {
                    required: true
                },
                colegio: {
                    required: true
                },
                subpla: {
                    required: true
                },
                fecafi: {
                    required: true
                },
                feccap: {
                    required: true
                },
                diahab: {
                    required: true
                }
            }
        });

        $('.js-basic-multiple, #codind, #tipsoc, #tipapo').select2();

        $('#aprobar_empresa').click(function(e) {
            e.preventDefault();
            var _target = $(e.currentTarget);
            aprobar_empresa(_target);
        });
    });

    function devolver_registro(id) {
        let _nota_devolver = $('#nota_devolver').val();

        if (_nota_devolver == "") {
            Messages.display("Digite la nota", 'error');
            return;
        }
        if ($("#codest_devolver").val() == "") {
            Messages.display("Digite el motivo de rechazo", 'error');
            return;
        }
        let _nota = $("#nota_devolver").val();
        let _token = {
            id: id,
            nota: _nota_devolver,
            codest: $("#codest_devolver").val(),
            campos_corregir: $("#campos_corregir").val()
        };
        $.ajax({
            dataType: 'JSON',
            method: "POST",
            url: Utils.getKumbiaURL($Kumbia.controller + "/devolver"),
            cache: false,
            data: _token
        }).done(function(response) {
            if (response.success) {
                swal.fire({
                    "title": "Notificación",
                    "text": response.msj,
                    "icon": "success",
                    "showConfirmButton": false,
                    "showCloseButton": true,
                    "timer": 10000
                });
                setTimeout(function() {
                    window.location = Utils.getKumbiaURL('aprobacionemp/index');
                }, 3000);
            } else {
                console.log(response.msj);
                swal.fire({
                    "title": "Notificación Error",
                    "text": response.msj,
                    "icon": "error",
                    "showConfirmButton": false,
                    "showCloseButton": true,
                    "timer": 10000
                });
            }
        }).fail(function(err) {
            console.log(err.responseText);
            swal.fire({
                "title": "Notificación Error",
                "text": err.responseText,
                "icon": "error",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 10000
            });
        });
    };

    function rechazar_registro(id) {
        let _nota_rechazar = $('#nota_rechazar').val();

        if (_nota_rechazar == "") {
            Messages.display("Digite la nota", 'error');
            return;
        }
        if ($("#codest").val() == "") {
            Messages.display("Digite el motivo de rechazo", 'error');
            return;
        }
        let _token = {
            id: id,
            nota: _nota_rechazar,
            codest: $("#codest").val()
        };
        $.ajax({
            dataType: 'JSON',
            method: "POST",
            url: Utils.getKumbiaURL($Kumbia.controller + "/rechazar"),
            cache: false,
            data: _token
        }).done(function(response) {
            if (response.success) {
                swal.fire({
                    "title": "Notificación",
                    "text": response.msj,
                    "icon": "success",
                    "showConfirmButton": false,
                    "showCloseButton": true,
                    "timer": 10000
                });
                setTimeout(function() {
                    window.location = Utils.getKumbiaURL('aprobacionemp/index');
                }, 8000);
            } else {
                swal.fire({
                    "title": "Notificación",
                    "text": response.msj,
                    "icon": "error",
                    "showConfirmButton": false,
                    "showCloseButton": true,
                    "timer": 10000
                });
            }
        }).fail(function(err) {
            swal.fire({
                "title": "Notificación Error",
                "text": err.responseText,
                "icon": "error",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 10000
            });
        });
    };

    function aprobar_empresa(_target) {
        if (!$("#form_aprobar").valid()) {
            return false;
        }
        let _nota_aprobar = $('#nota_aprobar').val();
        if (_nota_aprobar == "") {
            swal.fire({
                "title": "Notificación Alerta",
                "text": "Digíte la nota",
                "icon": "error",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 10000
            });
            return false;
        }
        _target.attr('disabled', true);
        let _token = {
            id: "<?= $id ?>",
            tipdur: $("#tipdur").val(),
            actapr: $("#actapr").val(),
            codind: $("#codind").val(),
            todmes: $("#todmes").val(),
            forpre: $("#forpre").val(),
            pymes: $("#pymes").val(),
            contratista: $("#contratista").val(),
            tipemp: $("#tipemp").val(),
            tipsoc: $("#tipsoc").val(),
            tipapo: $("#tipapo").val(),
            ofiafi: $("#ofiafi").val(),
            colegio: $("#colegio").val(),
            subpla: $("#subpla").val(),
            fecafi: $("#fecafi").val(),
            diahab: $("#diahab").val(),
            feccap: $("#feccap").val(),
            nota: _nota_aprobar
        };

        $.ajax({
            url: Utils.getKumbiaURL($Kumbia.controller + "/aprueba"),
            method: "POST",
            dataType: "JSON",
            cache: false,
            data: _token
        }).done(function(response) {
            _target.removeAttr('disabled');
            if (response.success) {
                swal.fire({
                    "title": "Notificación",
                    "text": response.msj,
                    "icon": "success",
                    "showConfirmButton": false,
                    "showCloseButton": true,
                    "timer": 10000
                });
                setTimeout(function() {
                    window.location = Utils.getKumbiaURL($Kumbia.controller + '/index');
                }, 6000);
            } else {
                swal.fire({
                    "title": "Notificación",
                    "text": response.msj,
                    "icon": "error",
                    "showConfirmButton": false,
                    "showCloseButton": true,
                    "timer": 10000
                });
                return false;
            }
        }).fail(function(err) {
            _target.removeAttr('disabled');
            swal.fire({
                "title": "Notificación Error",
                "text": err.responseText,
                "icon": "error",
                "showConfirmButton": false,
                "showCloseButton": true,
                "timer": 10000
            });
            return false;
        });
    }
</script>

<style>
    .note-editable {
        background-color: #FFFFFF;
    }

    .note-editable p,
    .note-editable h3,
    .note-editable label,
    .note-editable span {
        line-height: 1.8pt;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: .875rem;
        line-height: 1.5rem;
        display: inline-flex;
        margin: 0 0 0.25rem 0.25rem;
        padding: 0 0.5rem;
        color: #27561b;
        border: none;
        border-radius: 0.25rem;
        background-color: #c7ebcd;
    }

    .select2-results__option,
    .select2-results__option:hover .select2-results__option:focus {
        border-bottom: 1px solid #ededed;
        padding-top: 1px;
    }

    .form-control-label {
        font-size: .81rem;
        margin-bottom: 3px;
    }

    input.form-control,
    select.form-control {
        margin: 3px 0px 0px 3px;
        padding: 4px 6px;
        border-radius: 0px;
        height: initial;
        min-height: 20px;
        background-color: #fffeee;
    }

    .select2-container .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-search--dropdown .select2-search__field {
        height: calc(1.75rem + 1px);
        padding: 0.2rem 0.3rem;
        font-size: 14px;
    }
</style>
