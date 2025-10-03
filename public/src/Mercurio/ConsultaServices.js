import { Messages, Utils } from '@/Utils';
import { $App } from '@/App';

let validator = void 0;

const ConsultaTrabajadores = (e) => {
	validator = $('#form').validate({
		rules: {
			estado: { required: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	$.ajax({
		type: 'POST',
		url: $App.url('subsidioemp/consulta_trabajadores'),
		data: {
			estado: $('#estado').val(),
		},
	})
		.done(function (response) {
			if (response.flag == false) {
				Messages.display(response.msg, 'error');
			} else {
				$('#consulta').html(response.data);
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display('Request failed: ' + textStatus, 'error');
		});
};

const ConsultaGiro = () => {
	$.ajax({
		type: 'POST',
		url: $App.url('subsidioemp/consulta_giro'),
		data: {
			periodo: $('#periodo').val(),
		},
	})
		.done(function (response) {
			if (response.success == false) {
				Messages.display(response.msj, 'error');
			} else {
				$('#consulta').html(response.data);
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(textStatus.error, 'error');
			console.log('Request failed: ', textStatus);
		});
};

const ConsultaNomina = (e) => {
	validator = $('#form').validate({
		rules: {
			periodo: { required: true, date: false },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	$.ajax({
		type: 'POST',
		url: $App.url('subsidioemp/consulta_nomina'),
		data: {
			periodo: $('#periodo').val(),
		},
	})
		.done(function (response) {
			if (!response || response.success == false) {
				Messages.display(response.msg, 'error');
			} else {
				$('#consulta').html(response.data);
				$('[data-toggle="tooltip"]').tooltip();
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
};

const ConsultaAportes = (e) => {
	validator = $('#form').validate({
		rules: {
			perini: { required: true },
			perfin: {
				required: true,
				greaterThan: ['#perini', 'Periodo Inicial'],
			},
		},
	});
	if (!$('#form').valid()) {
		return;
	}

	$.ajax({
		type: 'POST',
		url: $App.url('subsidioemp/consulta_aportes'),
		data: {
			perini: $('#perini').val(),
			perfin: $('#perfin').val(),
		},
	})
		.done(function (transport) {
			const response = $.parseJSON(transport);
			if (response['flag'] == false) {
				Messages.display(response['msg'], 'error');
			} else {
				$('#consulta').html(response['data']);
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
};

const buscar_trabajador = () => {
	if ($('#cedtra').val() == '') {
		Messages.display('Digite la Cedula', 'error');
		return;
	}
	$.ajax({
		type: 'POST',
		url: $App.url('subsidioemp/buscar_trabajador'),
		data: {
			cedtra: $('#cedtra').val(),
		},
	})
		.done(function (transport) {
			const response = $.parseJSON(transport);
			if (response['flag'] == false) {
				Messages.display(response['msg'], 'error');
			} else {
				$('#nombre').val(response.data.nombre);
				$('#fecafi').val(response.data.fecafi);
				$('#cedtra').prop('disabled', true);
			}
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
};

const NovedadRetiro = () => {
	validator = $('#form').validate({
		rules: {
			cedtra: { required: true },
			nombre: { required: true },
			codest: { required: true },
			fecret: { required: true },
			archivo: { required: true },
			nota: { required: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	$('#archivo').upload(
		$App.url('subsidioemp/novedad_retiro'),
		{
			cedtra: $('#cedtra').val(),
			nombre: $('#nombre').val(),
			codest: $('#codest').val(),
			fecafi: $('#fecafi').val(),
			fecret: $('#fecret').val(),
			nota: $('#nota').val(),
		},
		function (transport) {
			const response = $.parseJSON(transport);
			if (response['flag'] == true) {
				Messages.display(response['msg'], 'success');
				window.location.reload();
			} else {
				Messages.display(response['msg'], 'error');
			}
		},
	);
};

const ActualizaDatosBasicosOld = () => {
	Swal.fire({
		title: 'Esta seguro de actualizar los datos?',
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
				url: $App.url('subsidioemp/actualiza_datos_basicos'),
				data: $('#form').serialize(),
			})
				.done(function (transport) {
					var response = $.parseJSON(transport);
					if (response['flag'] == true) {
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
};

const CertificadoAfiliacion = () => {
	validator = $('#form').validate({
		rules: {
			tipo: { required: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	$('#form').submit();
};

const CertificadoParaTrabajador = () => {
	validator = $('#form').validate({
		rules: {
			cedtra: { required: true },
			tipo: { required: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	$('#form').submit();
};

const AfiliaMasivaTrabajador = () => {
	if ($('#archivo').val() == '') {
		Messages.display('Adjunte algun Archivo', 'error');
		return;
	}
	$('#archivo').upload($App.url('subsidioemp/afilia_masiva_trabajador'), {}, function (response) {
		if (response['flag'] == true) {
			Messages.display(response['msg'], 'success');
		} else {
			Messages.display(response['msg'], 'error');
		}
		$('#consulta').html(response.data);
	});
};

const ActualizaDatosBasicos = () => {
	Swal.fire({
		title: 'Esta seguro de actualizar los datos?',
		text: '',
		type: 'warning',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success btn-fill',
		cancelButtonClass: 'btn btn-danger btn-fill',
		confirmButtonText: 'SI',
		cancelButtonText: 'NO',
	}).then((result) => {
		if (result.value) {
			$('#form').submit();
		}
	});
};

const EjemploPlanillaMasiva = () => {
	window.location.href = $App.url('ejemplo_planilla_masiva');
};

const EjemploPlanillaActivacionMasiva = () => {
	window.location.href = $App.url('ejemplo_planilla_activacion_masiva');
};

const ActivacionMasivaTrabajador = () => {
	if ($('#archivo').val() == '') {
		Messages.display('Adjunte algun Archivo', 'error');
		return;
	}
	$('#archivo').upload($App.url('subsidioemp/activacion_masiva_trabajador'), {}, function (response) {
		if (response['flag'] == true) {
			Messages.display(response['msg'], 'success');
			window.open(Utils.getURL(response['file']), '_blank');
			console.log('el poderoso');
		} else {
			Messages.display(response['msg'], 'error');
		}
		$('#consulta').html(response.data);
	});
};

const CambioEmail = () => {
	validator = $('#form').validate({
		rules: {
			email: { required: true, email: true },
		},
	});
	if (!$('#form').valid()) return false;
	Swal.fire({
		text: 'Esta seguro de actualizar el email de aviso?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success btn-fill',
		cancelButtonClass: 'btn btn-danger btn-fill',
		confirmButtonText: 'SI',
		cancelButtonText: 'NO',
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				type: 'POST',
				url: $App.url('cambio_email'),
				data: {
					email: $('#email').val(),
				},
			})
				.done(function (transport) {
					const response = $.parseJSON(transport);
					if (response['flag'] == false) {
						Messages.display(response['msg'], 'error');
					} else {
						Messages.display(response['msg'], 'success');
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert('Request failed: ' + textStatus);
				});
		}
	});
};

const CambioClave = () => {
	validator = $('#form').validate({
		rules: {
			claant: { required: true },
			clave: { required: true },
			clacon: { required: true },
		},
	});
	if (!$('#form').valid()) return false;
	Swal.fire({
		text: 'Esta seguro de cambiar la clave de acceso?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success btn-fill',
		cancelButtonClass: 'btn btn-danger btn-fill',
		confirmButtonText: 'SI',
		cancelButtonText: 'NO',
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				type: 'POST',
				url: $App.url('cambio_clave'),
				data: {
					claant: $('#claant').val(),
					clave: $('#clave').val(),
					clacon: $('#clacon').val(),
				},
			})
				.done(function (transport) {
					const response = $.parseJSON(transport);
					if (response['flag'] == false) {
						Messages.display(response['msg'], 'error');
					} else {
						Messages.display(response['msg'], 'success');
					}
				})
				.fail(function (jqXHR, textStatus) {
					alert('Request failed: ' + textStatus);
				});
		}
	});
};

export {
	ConsultaTrabajadores,
	ConsultaGiro,
	ConsultaNomina,
	ConsultaAportes,
	buscar_trabajador,
	ActivacionMasivaTrabajador,
	EjemploPlanillaActivacionMasiva,
	EjemploPlanillaMasiva,
	ActualizaDatosBasicos,
	AfiliaMasivaTrabajador,
	CertificadoParaTrabajador,
	CertificadoAfiliacion,
	ActualizaDatosBasicosOld,
	NovedadRetiro,
	CambioEmail,
	CambioClave,
};
