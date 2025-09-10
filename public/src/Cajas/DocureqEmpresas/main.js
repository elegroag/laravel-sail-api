import { LayoutGeneral } from '@/Cajas/LayoutGeneral';
import { Region } from '@/Common/Region';
import loading from '@/Componentes/Views/Loading';
import { $Kumbia, Messages, Utils } from '@/Utils';
import { addFiltro, aplicarFiltro, buscar, changeCantidadPagina, delFiltro } from '../Glob/Glob';

$(() => {
    const modalForm = new bootstrap.Modal(document.getElementById('modal_capturar_campo'));
    const region = new Region({ el: '#boneLayout' });
    const layout = new LayoutGeneral();
    region.show(layout);
    aplicarFiltro();

    const validator = $('#form').validate({
        rules: {
            coddoc: { required: true },
            tipopc: { required: true },
            tipsoc: { required: true },
            obliga: { required: true },
            nota: { required: false },
            auto_generado: { required: true },
        },
    });

    $(document).on('click', "[data-toggle='info']", (e) => {
        e.preventDefault();
        const coddoc = $(e.currentTarget).attr('data-coddoc');
        const tipopc = $(e.currentTarget).attr('data-tipopc');
        const tipsoc = $(e.currentTarget).attr('data-tipsoc');
        loading.show();
        $.ajax({
            type: 'POST',
            url: Utils.getKumbiaURL($Kumbia.controller + '/infor'),
            data: {
                coddoc: coddoc,
                tipopc: tipopc,
                tipsoc: tipsoc,
            },
        })
            .done((response) => {
                loading.hide();
                if (response.success == true) {
                    const data = response.data;
                    $.each(data, (key, value) => {
                        $('#' + key.toString()).val(value);
                    });

                    $('#coddoc').attr('disabled', 'true');
                    $('#tipopc').attr('disabled', 'true');
                    $('#tipsoc').attr('disabled', 'true');
                    modalForm.show();
                }
            })
            .fail((jqXHR, textStatus) => {
                Messages.display(jqXHR.statusText, 'error');
            });
    });

    $(document).on('click', "[data-toggle='guardar']", (e) => {
        e.preventDefault();

        $('#coddoc').removeAttr('disabled');
        $('#tipopc').removeAttr('disabled');
        $('#tipsoc').removeAttr('disabled');
        if (!validator.valid()) return;

        loading.show();
        $.ajax({
            type: 'POST',
            url: Utils.getKumbiaURL($Kumbia.controller + '/guardar'),
            data: {
                coddoc: $('#coddoc').val(),
                tipopc: $('#tipopc').val(),
                tipsoc: $('#tipsoc').val(),
                obliga: $('#obliga').val(),
                nota: $('#nota').val(),
                auto_generado: $('#auto_generado').val(),
            },
            dataType: 'json',
        })
            .done((response) => {
                loading.hide();
                if (response.success == true) {
                    Messages.display(response.msj, 'success');
                    modalForm.hide();
                    aplicarFiltro();
                } else {
                    Messages.display(response.msj, 'error');
                }
            })
            .fail((jqXHR, textStatus) => {
                Messages.display(jqXHR.statusText, 'error');
            });
    });

    $(document).on('click', "[data-toggle='borrar']", (e) => {
        e.preventDefault();
        const coddoc = $(e.currentTarget).attr('data-coddoc');
        const tipopc = $(e.currentTarget).attr('data-tipopc');
        const tipsoc = $(e.currentTarget).attr('data-tipsoc');
        Swal.fire({
            title: 'Confirmar!',
            html: 'Esta seguro de borrar el registro seleccionado ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success btn-fill',
            cancelButtonClass: 'btn btn-danger btn-fill',
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: Utils.getKumbiaURL($Kumbia.controller + '/borrar'),
                    dataType: 'json',
                    data: {
                        coddoc: coddoc,
                        tipopc: tipopc,
                        tipsoc: tipsoc,
                    },
                })
                    .done(function (response) {
                        if (response.success == true) {
                            Messages.display(response.msj, 'success');
                            aplicarFiltro();
                        } else {
                            Messages.display(response.msj, 'error');
                        }
                    })
                    .fail(function (jqXHR, textStatus) {
                        Messages.display(jqXHR.statusText, 'error');
                    });
            }
        });
    });

    $(document).on('click', "[data-toggle='nuevo']", (e) => {
        e.preventDefault();
        $('#coddoc').removeAttr('disabled');
        $('#tipopc').removeAttr('disabled');
        $('#tipsoc').removeAttr('disabled');
        $('#coddoc').val('');
        $('#tipopc').val('');
        $('#tipsoc').val('');
        $('#nota').val('');
        $('#obliga').val('');
        $('#auto_generado').val('');
        modalForm.show();
    });

    $(document).on('click', "[data-toggle='filtrar']", (e) => {
        e.preventDefault();
        const Modal = new bootstrap.Modal(document.getElementById('filtrar-modal'), {});
        Modal.show();
    });

    $(document).on('click', "[toggle-event='buscar']", (e) => {
        e.preventDefault();
        buscar($(e.currentTarget));
    });

    $(document).on('click', "[toggle-event='aplicar_filtro']", (e) => aplicarFiltro(e));

    $(document).on('click', "[toggle-event='add_filtro']", (e) => addFiltro(e));

    $(document).on('click', "[toggle-event='remove']", (e) => delFiltro($(e.currentTarget)));

    $(document).on('change', '#cantidad_paginate', (e) => changeCantidadPagina(e));
});
