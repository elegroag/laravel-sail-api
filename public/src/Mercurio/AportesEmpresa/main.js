import { langDataTable } from '@/Core';
import { $App } from '@/App';

window.App = $App;

$(() => {
	window.App.initialize();
	$(document).on('click', '#bt_consulta_aportes', (e) => {
		e.preventDefault();
		$('#form').validate({
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

		window.App.trigger('syncro', {
			url: window.App.url('subsidioemp/consulta_aportes'),
			data: {
				perini: $('#perini').val(),
				perfin: $('#perfin').val(),
			},
			callback: (response={}) => {
				if (!response || response.success == false) {
					window.App.trigger('alert:error', { message: response.msg });
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
								width: '10%',
							},
							{
								targets: 2,
								width: '20%',
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
				}
			}
		});
	});
});
