import { $App } from '@/App';
import {
	ConsultaGiro,
	ConsultaNoGiro,
	ConsultaPlanillaTrabajador,
	CertificadoAfiliacion,
} from '../TrabajadorServices';

$(() => {
	$App.initialize();

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

	$(document).on('click', '#bt_consulta_nogiro', function (e) {
		e.preventDefault();
		const valida = $('#form').validate({
			rules: {
				perini: { required: true, date: false },
				perfin: {
					required: true,
					date: false,
					greaterThan: ['#perini', 'Periodo Inicial'],
				},
			},
		});
		if (!valida.valid()) return;
		ConsultaNoGiro();
	});

	$(document).on('click', '#bt_consulta_planilla_trabajador', function (e) {
		e.preventDefault();
		const valida = $('#form').validate({
			rules: {
				perini: { required: true, date: false },
				perfin: {
					required: true,
					date: false,
					greaterThan: ['#perini', 'Periodo Inicial'],
				},
			},
		});
		if (!valida.valid()) return;
		ConsultaPlanillaTrabajador();
	});

	$(document).on('click', '#bt_certificado_afiliacion', CertificadoAfiliacion);

	$('#bt_consulta_giro').trigger('click');
	$('#bt_consulta_nogiro').trigger('click');
	$('#bt_consulta_planilla_trabajador').trigger('click');
});
