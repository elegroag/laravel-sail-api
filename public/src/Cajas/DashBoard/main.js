import { $App } from '@/App';
import { Messages } from '@/Utils';

const CHART_COLORS = [
	'rgba(37, 99, 235, 0.85)',
	'rgba(16, 185, 129, 0.85)',
	'rgba(245, 158, 11, 0.85)',
	'rgba(239, 68, 68, 0.85)',
	'rgba(139, 92, 246, 0.85)',
	'rgba(6, 182, 212, 0.85)',
	'rgba(236, 72, 153, 0.85)',
	'rgba(100, 116, 139, 0.85)',
];

const CHART_BORDERS = [
	'rgb(37, 99, 235)',
	'rgb(16, 185, 129)',
	'rgb(245, 158, 11)',
	'rgb(239, 68, 68)',
	'rgb(139, 92, 246)',
	'rgb(6, 182, 212)',
	'rgb(236, 72, 153)',
	'rgb(100, 116, 139)',
];

const paletteFor = (count) => {
	const n = Math.max(count || 0, 1);
	const backgroundColor = [];
	const borderColor = [];
	for (let i = 0; i < n; i += 1) {
		backgroundColor.push(CHART_COLORS[i % CHART_COLORS.length]);
		borderColor.push(CHART_BORDERS[i % CHART_BORDERS.length]);
	}
	return { backgroundColor, borderColor };
};

const buildDoughnutOptions = () => ({
	responsive: true,
	maintainAspectRatio: false,
	cutoutPercentage: 58,
	legend: {
		position: 'bottom',
		labels: {
			boxWidth: 12,
			padding: 16,
			usePointStyle: true,
			fontColor: '#64748b',
		},
	},
	tooltips: {
		backgroundColor: 'rgba(15, 23, 42, 0.92)',
		xPadding: 10,
		yPadding: 10,
		cornerRadius: 8,
	},
	layout: {
		padding: 8,
	},
});

const buildBarOptions = () => ({
	responsive: true,
	maintainAspectRatio: false,
	legend: {
		display: false,
	},
	tooltips: {
		backgroundColor: 'rgba(15, 23, 42, 0.92)',
		xPadding: 10,
		yPadding: 10,
		cornerRadius: 8,
	},
	scales: {
		xAxes: [
			{
				gridLines: {
					display: false,
					drawBorder: false,
				},
				ticks: {
					fontColor: '#64748b',
				},
			},
		],
		yAxes: [
			{
				ticks: {
					beginAtZero: true,
					fontColor: '#64748b',
					precision: 0,
				},
				gridLines: {
					color: 'rgba(148, 163, 184, 0.25)',
					zeroLineColor: 'rgba(148, 163, 184, 0.35)',
				},
			},
		],
	},
	layout: {
		padding: 8,
	},
});

const renderDoughnut = (canvasId, labels, data) => {
	const canvas = document.getElementById(canvasId);
	if (!canvas) return;
	const values = Array.isArray(data) ? data : [];
	const chartLabels = Array.isArray(labels) ? labels : [];
	const { backgroundColor, borderColor } = paletteFor(values.length);
	new Chart(canvas.getContext('2d'), {
		type: 'doughnut',
		data: {
			labels: chartLabels,
			datasets: [
				{
					data: values,
					backgroundColor,
					borderColor,
					borderWidth: 2,
				},
			],
		},
		options: buildDoughnutOptions(),
	});
};

const traerUsuariosRegistrados = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_usuarios_registrados',
		data: {},
		callback: (response) => {
			renderDoughnut('chart-usuarios', response.labels, response.data);
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		},
	});
};

const traerOpcionMasUsuada = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_opcion_mas_usada',
		data: {},
		callback: (response) => {
			renderDoughnut('chart-opcion', response.labels, response.data);
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		},
	});
};

const traerMotivoMasUsuada = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_motivo_mas_usada',
		data: {},
		callback: (response) => {
			renderDoughnut('chart-rechazo', response.labels, response.data);
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		},
	});
};

const traerCargaLaboral = function () {
	$App.trigger('ajax', {
		type: 'POST',
		url: window.ServerController + '/traer_carga_laboral',
		data: {},
		callback: (response) => {
			const canvas = document.getElementById('chart-laboral');
			if (!canvas) return;
			const values = Array.isArray(response.data) ? response.data : [];
			const { backgroundColor, borderColor } = paletteFor(values.length);
			new Chart(canvas.getContext('2d'), {
				type: 'bar',
				data: {
					labels: response.labels || [],
					datasets: [
						{
							data: values,
							backgroundColor,
							borderColor,
							borderWidth: 1,
						},
					],
				},
				options: buildBarOptions(),
			});
		},
		error: (jqXHR) => {
			Messages.display(jqXHR.statusText, 'error');
		},
	});
};

$(() => {
	$App.initialize();
	traerUsuariosRegistrados();
	traerOpcionMasUsuada();
	traerMotivoMasUsuada();
	traerCargaLaboral();
});
