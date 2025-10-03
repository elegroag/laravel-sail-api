
import { Messages } from '@/Utils';

const TraerAportesEmpresa = () => {
	window.App.trigger('syncro', {
		url: window.App.url('principal/traer_aportes_empresa'), 
		data: {}, 
		silent: false,
		callback: (response) => {
			if (response.success === true) {
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
				Messages.display(response.message, 'error');
			}
		}
	});
};

const TraerCategoriasEmpresa = () => {
	window.App.trigger('syncro', {
		url: window.App.url('principal/traer_categorias_empresa'),
		data: {},
		silent: false,
		callback: (response) => {
			if (response.success === true) {
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
				Messages.display(response.message, 'error');
			}
		}
	});
};

const TraerGiroEmpresa = () => {
	window.App.trigger('syncro', {
		url: window.App.url('principal/traer_giro_empresa'),
		data: {},
		silent: false,
		callback: (response) => {
			if (response.success === true) {
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
			} else {
				$('#render_chart_giro').html(
					"<div class='card'><div class='card-body'><h5>Informe giro empresa</h5><p>La empresa no posee datos suficientes de giro.</p></div></div>",
				);
				Messages.display(response.message, 'error');
			}
		}
	});
};	

export { TraerAportesEmpresa, TraerGiroEmpresa, TraerCategoriasEmpresa };
