import { $App } from '@/App';
import { Messages } from '@/Utils';

const traerUsuariosRegistrados = function () {
	$.ajax({
		type: 'POST',
		url: $App.url('traerUsuariosRegistrados'),
		data: {},
	})
		.done((response) => {
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
		})
		.fail((jqXHR, textStatus) => {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const traerOpcionMasUsuada = function () {
	$.ajax({
		type: 'POST',
		url: $App.url('traerOpcionMasUsuada'),
		data: {},
	})
		.done((response) => {
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
		})
		.fail((jqXHR, textStatus) => {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const traerMotivoMasUsuada = function () {
	$.ajax({
		type: 'POST',
		url: $App.url('traerMotivoMasUsuada'),
		data: {},
	})
		.done((response) => {
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
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const traerCargaLaboral = function () {
	$.ajax({
		type: 'POST',
		url: $App.url('traerCargaLaboral'),
		data: {},
	})
		.done((response) => {
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
		})
		.fail((jqXHR, textStatus) => {
			Messages.display(jqXHR.statusText, 'error');
		});
};

$(() => {
	traerUsuariosRegistrados();
	traerOpcionMasUsuada();
	traerMotivoMasUsuada();
	traerCargaLaboral();
});
