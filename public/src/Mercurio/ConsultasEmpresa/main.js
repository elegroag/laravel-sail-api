import { $App } from '@/App';
import {
	ActivacionMasivaTrabajador,
	ActualizaDatosBasicos,
	AfiliaMasivaTrabajador,
	CambioClave,
	CambioEmail,
	CertificadoAfiliacion,
	ConsultaAportes,
	ConsultaNomina,
	EjemploPlanillaActivacionMasiva,
	EjemploPlanillaMasiva,
	NovedadRetiro,
	ConsultaTrabajadores,
} from '../ConsultaServices';
import {
	ConsultaConyugeView,
	ConsultaBeneficiarioView,
} from '../ConsultaNucleo/ConsultaTrabajadorView';
import { Region } from '@/Common/Region';

import { ConsultaGiro } from '../TrabajadorServices';

window.App = $App;

$(() => {
	window.App.initialize();

	$(document).on('click', '#bt_consulta_giro', function (e) {
		e.preventDefault();
		const valida = $('#form').validate({
			rules: {
				perini: { required: true, date: false },
				perfin: { required: true, date: false },
			},
		});

		if (!valida.valid()) return;
		ConsultaGiro();
	});

	$(document).on('click', '#btn_consulta_nomina', ConsultaNomina);

	$(document).on('click', '#bt_consulta_aportes', ConsultaAportes);

	$(document).on('click', '#bt_certificado_afiliacion', CertificadoAfiliacion);

	$(document).on('click', '#bt_certificado_afiliacion', CertificadoAfiliacion);

	$(document).on('click', '#bt_activacion_masiva_trabajador', ActivacionMasivaTrabajador);

	$(document).on('click', '#bt_actualiza_datos_basicos', ActualizaDatosBasicos);

	$(document).on(
		'click',
		'#bt_ejemplo_planilla_activacion_masiva',
		EjemploPlanillaActivacionMasiva,
	);

	$(document).on('click', '#bt_ejemplo_planilla_masiva', EjemploPlanillaMasiva);

	$(document).on('click', '#bt_afilia_masiva_trabajador', AfiliaMasivaTrabajador);

	$(document).on('click', '#bt_certificado_afiliacion', CertificadoAfiliacion);

	$(document).on('click', '#bt_novedad_retiro', NovedadRetiro);

	$(document).on('click', '#bt_cambio_email', CambioEmail);

	$(document).on('click', '#bt_cambio_clave', CambioClave);

	$(document).on('click', '#bt_consulta_trabajadores', ConsultaTrabajadores);

	$(document).on('click', '[data-event="ver_nucleo_familiar"]', function (e) {
		e.preventDefault();
		const cedtra = $(e.currentTarget).data('cedtra');
		if (!cedtra) return;
		$.ajax({
			type: 'POST',
			url: window.App.url('subsidioemp/consulta_nucleo'),
			data: {
				cedtra: cedtra,
			},
		})
			.done(function (response) {
				if (response.success == false) {
					Messages.display(response.msj, 'error');
				} else {
					const modal = new bootstrap.Modal(
						document.getElementById('modalConsultaNucleo'),
						{
							keyboard: true,
							backdrop: 'static',
						},
					);

					const viewConyuges = new ConsultaConyugeView({
						model: {
							...response.data.params,
							conyuges: response.data.conyuges,
						},
					});

					const regionConyuges = new Region({ el: '#render_conyuges' });
					regionConyuges.show(viewConyuges);

					const viewBeneficiarios = new ConsultaBeneficiarioView({
						model: {
							...response.data.params,
							beneficiarios: response.data.beneficiarios,
						},
					});
					const regionBeneficiarios = new Region({ el: '#render_beneficiarios' });
					regionBeneficiarios.show(viewBeneficiarios);
					modal.show();
				}
			})
			.fail(function (jqXHR, textStatus) {
				Messages.display(textStatus.error, 'error');
			});
	});
});
