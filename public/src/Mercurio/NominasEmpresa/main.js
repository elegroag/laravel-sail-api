import { langDataTable } from '../../Core';
import { $App } from '../../App';

$(() => {
	$App.initialize();
	$(document).on('click', '#btn_consulta_nomina', (e) => {
		e.preventDefault();
		$('#form').validate({
			rules: {
				periodo: {
					required: true,
				}
			},
			messages: {
				periodo: {
					required: 'El campo periodo de nomina es un valor obligatorio para la consulta.',
				}
			},
		});

		if (!$('#form').valid()) {
			return;
		}

		$App.trigger('syncro', {
			url: $App.url('consulta_nomina'),
			data: {
				periodo: $('#periodo').val()
			},
			callback: (response={}) => {
				if (!response || response.success == false) {
					$App.trigger('alert:error', { message: response.msg });
				} else {
					$('#consulta').html(response.data);
					$('#dataTable').DataTable({
						paging: true,
						ordering: true,
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
								width: '30%',
							},
							{
								targets: 2,
								width: '10%',
							},
							{
								targets: 3,
								width: '10%',
							},
							{
								targets: 4,
								width: '10%',
							},
							{
								targets: 5,
								width: '5%',
							},
							{
								targets: 6,
								width: '5%',
							},
							{
								targets: 7,
								width: '10%',
							},
							{
								targets: 8,
								width: '10%',
							},
							{
								targets: 9,
								width: '10%',
							},
							{
								targets: 10,
								width: '10%',
							},
						],
						order: [[0, 'desc']],
						language: langDataTable
					});
				}
			}
		});
	});
});
