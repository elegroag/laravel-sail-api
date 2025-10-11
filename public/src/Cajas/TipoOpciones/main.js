import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

var validator;
window.App = $App;
const validatorInit = () => {
    validator = $('#form').validate({
        rules: {
            tipopc: { required: true },
            detalle: { required: true },
            dias: { required: true },
        },
    });
};
const ArchivosEmpresaView = (tipopc, cp) => {
	const tipsoc = $('#tipsoc').val();
	window.App.trigger('ajax', {
		url: window.ServerController + '/archivos_empresa_view',
		data: {
			tipopc: tipopc,
			tipsoc: tipsoc,
		},
        callback: (response) => {
            if (response) {
                cp(response);
            }
        },
        error: (xhr) => {
            Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
	});
};

const ArchivosView = (tipopc, cp) => {
	window.App.trigger('ajax', {
		url: window.ServerController + '/archivos_view',
		data: {
			tipopc
		},
        callback: (response) => {
            if (response) {
                cp(response);
            }
        },
        error: (xhr) => {
            Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
        }
	});
};

$(() => {
    window.App.initialize();
	EventsPagination();

	const modalArchivos = new bootstrap.Modal(
		document.getElementById('capturaArchivos'),
	);

	const modalEmpresa = new bootstrap.Modal(
		document.getElementById('capturaEmpresa'),
	);

	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));

	$(document).on('blur', '#tipopc', function () {
		validePk('#tipopc');
	});

	$(document).on('click', "[data-toggle='empresa-view']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		ArchivosEmpresaView(tipopc, (response) => {
            modalEmpresa.show();
            $('#capturaEmpresabody').html(response);
            $('#tipsoc').val('');
		});
	});

	$(document).on('click', "[data-toggle='archivos-view']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		ArchivosView(tipopc, (response) => {
            $('#capturaArchivosbody').html(response);
			modalArchivos.show();
		});
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		  window.App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/editar'),
			data: {
				tipopc
			},
            callback: (response) => {
                if (response) {
                    modalCapture.show();
                    const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl(response.data));
                    validatorInit();

                    $('#tipopc_edit').val(response.data.tipopc || '');
                    $('#detalle_edit').val(response.data.detalle || '');
                    $('#dias_edit').val(response.data.dias || '');
                } else {
                    Messages.display('No se pudieron cargar los datos', 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al cargar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!validator.valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});
		  window.App.trigger('syncro', {
            url: window.App.url(window.ServerController +'/guardar'),
			data: $('#form').serialize(),
            callback: (response) => {
                if (response['flag'] == true) {
                    buscar();
                    Messages.display(response['msg'], 'success');
                    modalCapture.hide();
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
		});
	});

	$(document).on('click', "[data-toggle='borrar']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		Swal.fire({
			title: 'Esta seguro de borrar?',
			text: '',
			type: 'warning',
			showCancelButton: true,
			confirmButtonClass: 'btn btn-success btn-fill',
			cancelButtonClass: 'btn btn-danger btn-fill',
			confirmButtonText: 'SI',
			cancelButtonText: 'NO',
		}).then((result) => {
			if (result.value) {
				window.App.trigger('syncro', {
                    url: window.App.url(window.ServerController +'/borrar'),
					data: {
						tipopc: tipopc,
					},
                    callback: (response) => {
                        if (response['flag'] == true) {
                            buscar();
                            Messages.display(response['msg'], 'success');
                        } else {
                            Messages.display(response['msg'], 'error');
                        }
                    }
			    });
			}
		});
	});

	$(document).on('click', "[data-toggle='header-nuevo']", (e) => {
        e.preventDefault();
        $('#form :input').each(function (elem) {
            $(this).val('');
            $(this).removeAttr('disabled');
        });

        const tpl = _.template(document.getElementById('tmp_form').innerHTML);
        $('#captureModalbody').html(tpl({
            tipopc: '',
            detalle: '',
            dias: '',
        }));
        modalCapture.show();
        validatorInit();
    });

	$(document).on('click', "[data-toggle='empresa-guardar']", (e) => {
		e.preventDefault();
		const id = $(e.currentTarget).attr('id');
		const tipopc = $(e.currentTarget).attr('data-tipopc');

		let acc;
		if ($('#' + id).prop('checked') == false) acc = '0';
		if ($('#' + id).prop('checked') == true) acc = '1';

		  window.App.trigger('syncro', {
            url: window.App.url(window.ServerController +'/guardar-empresa-archivos'),
            data: {
				tipopc: tipopc,
				tipsoc: $('#tipsoc').val(),
				coddoc: $('#' + id).val(),
				acc: acc,
			},
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    ArchivosEmpresaView(tipopc, (response) => {
                        $('#div_archivos_empresa').html(response);
					});
				} else {
					Messages.display(response['msg'], 'error');
				}
			},
			error: (xhr) => {
				Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
			}
		});
	});

	$(document).on('click', "[data-toggle='archivo-guardar']", (e) => {
		const id = $(e.currentTarget).attr('id');
		const tipopc = $(e.currentTarget).attr('data-tipopc');
        const coddoc = $(e.currentTarget).attr('data-coddoc');

		let acc;
		if ($('#' + id).prop('checked') == false) acc = '0';
		if ($('#' + id).prop('checked') == true) acc = '1';

		window.App.trigger('syncro', {
            url: window.App.url(window.ServerController +'/guardar_archivos'),
            data: {
				tipopc,
                coddoc,
				acc
			},
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                    ArchivosEmpresaView(tipopc, () => {
                        modalArchivos.show();
					});
				} else {
					Messages.display(response['msg'], 'error');
				}
			},
			error: (xhr) => {
				Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
			}
		});
    });

	$(document).on('click', "[data-toggle='empresa-archivo-obliga']", (e) => {
		const coddoc = $(e.currentTarget).attr('data-coddoc');
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		const id = $(e.currentTarget).attr('id');
		let value;
		if ($('#' + id).prop('checked') == false) value = 'N';
		if ($('#' + id).prop('checked') == true) value = 'S';
		window.App.trigger('syncro', {
            url: window.App.url(window.ServerController +'/obliga_empresa_archivos'),
            data: {
				tipsoc: $('#tipsoc').val(),
				tipopc: tipopc,
				coddoc: coddoc,
				obliga: value,
			},
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
	});

	$(document).on('click', "[data-toggle='archivo-obliga']", (e) => {
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		const coddoc = $(e.currentTarget).attr('data-coddoc');
		const id = $(e.currentTarget).attr('id');
		let value;
		if ($('#' + id).prop('checked') == false) value = 'N';
		if ($('#' + id).prop('checked') == true) value = 'S';

		window.App.trigger('syncro', {
            url: window.App.url(window.ServerController +'/obliga_archivos'),
            data: {
				tipopc: tipopc,
				coddoc: coddoc,
				obliga: value,
			},
            callback: (response) => {
                if (response['flag'] == true) {
                    Messages.display(response['msg'], 'success');
                } else {
                    Messages.display(response['msg'], 'error');
                }
            },
            error: (xhr) => {
                Messages.display('Error al guardar los datos: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
	});

	$(document).on('click', "[data-toggle='empresas-archivos']", (e) => {
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		ArchivosEmpresaView(tipopc, (response) => {
			$('#div_archivos_empresa').html(response);
			modalArchivos.show();
		});
	});

	$(document).on('change', '#tipsoc', (e) => {
		ArchivosEmpresaView(Tipopc, (response) => {
			$('#div_archivos_empresa').html(response);
			modalArchivos.show();
		});
	});

	$(document).on('click', "[data-toggle='empresas-archivos']", (e) => {
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		ArchivosEmpresaView(tipopc, (response) => {
			$('#div_archivos_empresa').html(response);
			modalArchivos.show();
		});
	});

	$(document).on('change', '#tipsoc', (e) => {
		ArchivosEmpresaView(Tipopc, (response) => {
			$('#div_archivos_empresa').html(response);
		});
	});
});
