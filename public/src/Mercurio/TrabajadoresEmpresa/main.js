import { langDataTable } from '../../Core';
import { $App } from '../../App';
import { Region } from '../../Common/Region';
import { ConsultaBeneficiarioView, ConsultaConyugeView } from '../ConsultaNucleo/ConsultaTrabajadorView';

window.App = $App;

$(() => {
	window.App.initialize();
	$(document).on('click', '#bt_consulta_trabajadores', function (e) {
		$('#form').validate({
			rules: {
				estado: { required: true },
			},
		});

		if (!$('#form').valid()) return;

		$App.trigger('syncro', {
			url: $App.url('consulta_trabajadores'),
			data: {
				estado: $('#estado').val(),
			},
			callback: (response) => {
				if (response && response.success) {
					$('#consulta').html(response.data);
					$('#dataTable').DataTable({
						paging: true,
						ordering: false,
						pageLength: 10,
						pagingType: 'numbers',
						info: true,
						searching: true,
						columnDefs: [
							{
								targets: 0,
								width: '5%',
							},
							{
								targets: 1,
								width: '10%',
							},
							{
								targets: 2,
								width: '40%',
							},
							{
								targets: 3,
								width: '15%',
							},
							{
								targets: 4,
								width: '10%',
							},
						],
						order: [[0, 'desc']],
						language: langDataTable
					});

				} else {
					$App.trigger('alert:error', {
						message: response.msg,
					});
				}
			}
		});
	});

	$(document).on('click', '[data-event="ver_nucleo_familiar"]', function (e) {
		e.preventDefault();
		const cedtra = $(e.currentTarget).data('cedtra');
		if (!cedtra) return;

		const modal = new bootstrap.Modal(
			document.getElementById('modalConsultaNucleo'),
			{
				keyboard: true,
				backdrop: 'static',
			},
		);

		$App.trigger('syncro', {
			url: $App.url('consulta_nucleo'),
			data: {
				cedtra: cedtra,
			},
			callback: (response) => {
				if (!response || response.success == false) {
					$App.trigger('alert:error', {
						message: response.msj,
					});
				} else {

					if (response.data.conyuges) {
						const viewConyuges = new ConsultaConyugeView({
							model: {
								...response.data.params,
								conyuges: response.data.conyuges,
							},
						});

						const regionConyuges = new Region({ el: '#render_conyuges' });
						regionConyuges.show(viewConyuges);
					} else {
						$('#render_conyuges').text('No hay datos de conyuge asociado con el trabajador');
					}

					if (response.data.beneficiarios) {
						const viewBeneficiarios = new ConsultaBeneficiarioView({
							model: {
								...response.data.params,
								beneficiarios: response.data.beneficiarios,
							},
						});
						const regionBeneficiarios = new Region({ el: '#render_beneficiarios' });
						regionBeneficiarios.show(viewBeneficiarios);
					} else {
						$('#render_beneficiarios').text('No hay datos de beneficiarios asociados con el trabajador');
					}
					modal.show();
				}
			}
		});
	});

	$(document).on('change', "#estado", function (e) {
		$('#consulta').html('');
		const estado = $(e.currentTarget).val();
		if (estado) {
			$('#bt_consulta_trabajadores').trigger('click');
		}
	});
});
