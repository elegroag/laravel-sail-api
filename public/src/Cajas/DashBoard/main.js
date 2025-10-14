import { $App } from '@/App';
import { Messages } from '@/Utils';

const traerUsuariosRegistrados = function () {
	$App.trigger('ajax',{
		type: 'POST',
		url: window.ServerController + '/traer_usuarios_registrados',
		data: {},
		callback: (response) => {
			const ctx = document.getElementById('chart-usuarios').getContext('2d');
			new Chart(ctx, {
				type: 'pie',
				data: {
					labels: response.labels,
					datasets: [
						{
							data: response.data,
							borderWidth: 1,
							backgroundColor: [
								'rgb(255, 99, 132)',
								'rgb(54, 162, 235)',
								'rgb(255, 205, 86)',
							],
						},
					],
				},
			});
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		}
	});
};

const traerOpcionMasUsuada = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_opcion_mas_usada',
		data: {},
		callback: (response) => {
			const ctx = document.getElementById('chart-opcion').getContext('2d');
			new Chart(ctx, {
				type: 'pie',
				data: {
					labels: response.labels,
					datasets: [
						{
							data: response.data,
							backgroundColor: [
								'rgb(255, 99, 132)',
								'rgb(54, 162, 235)',
								'rgb(255, 205, 86)',
							],
						},
					],
				},
			});
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		}
	});
};

const traerMotivoMasUsuada = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_motivo_mas_usada',
		data: {},
		callback: (response) => {
			const ctx = document.getElementById('chart-rechazo').getContext('2d');
			new Chart(ctx, {
				type: 'pie',
				data: {
					labels: response.labels,
					datasets: [
						{
							data: response.data,
							borderWidth: 1,
							backgroundColor: [
								'rgb(255, 99, 132)',
								'rgb(54, 162, 235)',
								'rgb(255, 205, 86)',
							],
						},
					],
				},
			});
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		}
	});
};

const traerCargaLaboral = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_carga_laboral',
		data: {},
		callback: (response) => {
			const ctx = document.getElementById('chart-laboral').getContext('2d');
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: response.labels,
					datasets: [
						{
							data: response.data,
							borderWidth: 1,
						},
					],
				},
				options: {
					scales: {
						yAxes: [
							{
								ticks: {
									beginAtZero: true,
								},
							},
						],
					},
				},
			});
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		}
	});
};

$(() => {
	$App.initialize();
	traerUsuariosRegistrados();
	traerOpcionMasUsuada();
	traerMotivoMasUsuada();
	traerCargaLaboral();
});
