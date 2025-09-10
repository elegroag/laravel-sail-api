import { $App } from '@/App';
import { Messages } from '@/Utils';
import { actualizar_select, buscar, EventsPagination, validePk } from '../Glob/Glob';

var validator;

const ArchivosEmpresaView = (tipopc, cp) => {
	const tipsoc = $('#tipsoc').val();
	$.ajax({
		type: 'POST',
		url: $App.url('archivos_empresa_view'),
		data: {
			tipopc: tipopc,
			tipsoc: tipsoc,
		},
	})
		.done(function (response) {
			if (response) {
				cp(response);
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
};

const ArchivosView = (tipopc, cp) => {
	$.ajax({
		type: 'POST',
		url: $App.url('archivos_view'),
		data: {
			tipopc: tipopc,
		},
	})
		.done(function (response) {
			if (response) {
				cp(response);
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
};

$(() => {
	$App.initialize();
	EventsPagination();

	let Tipopc = undefined;

	const modalArchivos = new bootstrap.Modal(
		document.getElementById('ModalCapturaArchivos'),
	);

	const modalEmpresa = new bootstrap.Modal(
		document.getElementById('ModalCapturaEmpresa'),
	);

	const modalCapture = new bootstrap.Modal(document.getElementById('capture-modal'));

	validator = $('#form').validate({
		rules: {
			tipopc: { required: true },
			detalle: { required: true },
			dias: { required: true },
		},
	});

	$(document).on('blur', '#tipopc', function () {
		validePk('#tipopc');
	});

	$('#capture-modal').on('hide.bs.modal', function (e) {
		validator.resetForm();
		$('.select2-selection')
			.removeClass(validator.settings.errorClass)
			.removeClass(validator.settings.validClass);
	});

	$(document).on('click', "[data-toggle='empresa-view']", (e) => {
		e.preventDefault();
		Tipopc = $(e.currentTarget).attr('data-cid');
		$('#div_archivos_empresa').html('');
		modalEmpresa.show();
		$('#tipsoc').val('');
	});

	$(document).on('click', "[data-toggle='archivos-view']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		ArchivosView(tipopc, (response) => {
			$('#div_archivos').html(response);
			modalArchivos.show();
		});
	});

	$(document).on('click', "[data-toggle='editar']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		$.ajax({
			type: 'POST',
			url: $App.url('editar'),
			data: {
				tipopc: tipopc,
			},
		})
			.done(function (response) {
				$.each(response, function (key, value) {
					$('#' + key.toString()).val(value);
				});
				$('#tipopc').attr('disabled', 'true');
				modalCapture.show();
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
			});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		e.preventDefault();
		if (!validator.valid()) return;

		$('#form :input').each(function (elem) {
			$(this).removeAttr('disabled');
		});
		$.ajax({
			type: 'POST',
			url: $App.url('guardar'),
			data: $('#form').serialize(),
		})
			.done(function (response) {
				if (response['flag'] == true) {
					buscar();
					Messages.display(response['msg'], 'success');
					modalCapture.hide();
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(jqXHR.statusText, 'error');
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
				$.ajax({
					type: 'POST',
					url: $App.url('borrar'),
					data: {
						tipopc: tipopc,
					},
				})
					.done(function (response) {
						if (response['flag'] == true) {
							buscar();
							Messages.display(response['msg'], 'success');
						} else {
							Messages.display(response['msg'], 'error');
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
		$('#form :input').each(function (elem) {
			$(this).val('');
			$(this).removeAttr('disabled');
		});
		actualizar_select();
		modalCapture.show();
	});

	$(document).on('click', "[data-toggle='empresa-guardar']", (e) => {
		e.preventDefault();
		const id = $(e.currentTarget).attr('id');
		const tipopc = $(e.currentTarget).attr('data-tipopc');

		let acc;
		if ($('#' + id).prop('checked') == false) acc = '0';
		if ($('#' + id).prop('checked') == true) acc = '1';

		$.ajax({
			type: 'POST',
			url: $App.url('guardarEmpresaArchivos'),
			data: {
				tipopc: tipopc,
				tipsoc: $('#tipsoc').val(),
				coddoc: $('#' + id).val(),
				acc: acc,
			},
		})
			.done(function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					ArchivosEmpresaView(tipopc, (response) => {
						$('#div_archivos_empresa').html(response);
					});
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='archivo-guardar']", (e) => {
		const id = $(e.currentTarget).attr('id');
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		let acc;
		if ($('#' + id).prop('checked') == false) acc = '0';
		if ($('#' + id).prop('checked') == true) acc = '1';

		$.ajax({
			type: 'POST',
			url: $App.url('guardarArchivos'),
			data: {
				tipopc: tipopc,
				coddoc: $('#' + id).val(),
				acc: acc,
			},
		})
			.done(function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					ArchivosEmpresaView(tipopc, () => {
						modalArchivos.show();
					});
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='empresa-archivo-obliga']", (e) => {
		const coddoc = $(e.currentTarget).attr('data-coddoc');
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		const id = $(e.currentTarget).attr('id');
		let value;
		if ($('#' + id).prop('checked') == false) value = 'N';
		if ($('#' + id).prop('checked') == true) value = 'S';
		$.ajax({
			type: 'POST',
			url: $App.url('obligaEmpresaArchivos'),
			data: {
				tipsoc: $('#tipsoc').val(),
				tipopc: tipopc,
				coddoc: coddoc,
				obliga: value,
			},
		})
			.done(function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='archivo-obliga']", (e) => {
		const tipopc = $(e.currentTarget).attr('data-tipopc');
		const coddoc = $(e.currentTarget).attr('data-coddoc');
		const id = $(e.currentTarget).attr('id');
		let value;
		if ($('#' + id).prop('checked') == false) value = 'N';
		if ($('#' + id).prop('checked') == true) value = 'S';

		$.ajax({
			type: 'POST',
			url: $App.url('obligaArchivos'),
			data: {
				tipopc: tipopc,
				coddoc: coddoc,
				obliga: value,
			},
		})
			.done(function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
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
