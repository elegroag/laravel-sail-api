import { $App } from '@/App';
import { Messages } from '@/Utils';

const TraerAportesEmpresa = () => {
	$.ajax({
		type: 'POST',
		url: $App.url('traerAportesEmpresa'),
		data: {},
	})
		.done((response) => {
			if (response && _.size(response.data) > 0) {
				const ctx = document.getElementById('chart-aportes').getContext('2d');
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
			} else {
				$('#render_chart_aportes').html(
					"<div class='card'><div class='card-body'><h5>Informe de aportes</h5><p>La empresa no posee datos relacionados a los aportes realizados. </p></div></div>",
				);
			}
		})
		.fail((jqXHR, textStatus) => {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const TraerCategoriasEmpresa = () => {
	$.ajax({
		type: 'POST',
		url: $App.url('traerCategoriasEmpresa'),
		data: {},
	})
		.done(function (response) {
			if (_.size(response.data) > 0) {
				var ctx = document.getElementById('chart-categorias').getContext('2d');
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
			} else {
				$('#render_chart_categorias').html(
					"<div class='card'><div class='card-body'><h5>Informe trabajadores por categorias</h5><p>La empresa no posee datos suficientes de trabajadores por categorias.</p></div></div>",
				);
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const TraerGiroEmpresa = () => {
	$.ajax({
		type: 'POST',
		url: $App.url('traerGiroEmpresa'),
		data: {},
	})
		.done(function (response) {
			const  ctx = document.getElementById('chart-giro').getContext('2d');
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
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

export { TraerAportesEmpresa, TraerGiroEmpresa, TraerCategoriasEmpresa };
