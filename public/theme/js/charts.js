('use strict');

var Charts = (function () {
	// Variable

	var $toggle = $('[data-toggle="chart"]');
	var mode = 'light'; //(themeMode) ? themeMode : 'light';
	var fonts = {
		base: 'Open Sans',
	};

	// Colors
	var colors = {
		gray: {
			100: '#f6f9fc',
			200: '#e9ecef',
			300: '#dee2e6',
			400: '#ced4da',
			500: '#adb5bd',
			600: '#8898aa',
			700: '#525f7f',
			800: '#32325d',
			900: '#212529',
		},
		theme: {
			default: '#172b4d',
			primary: '#5e72e4',
			secondary: '#f4f5f7',
			info: '#11cdef',
			success: '#2dce89',
			danger: '#f5365c',
			warning: '#fb6340',
		},
		black: '#12263F',
		white: '#FFFFFF',
		transparent: 'transparent',
	};

	// Methods

	// Chart.js global options
	function chartOptions() {
		// Options
		var options = {
			defaults: {
				global: {
					responsive: true,
					maintainAspectRatio: false,
					defaultColor: mode == 'dark' ? colors.gray[700] : colors.gray[600],
					defaultFontColor: mode == 'dark' ? colors.gray[700] : colors.gray[600],
					defaultFontFamily: fonts.base,
					defaultFontSize: 13,
					layout: {
						padding: 0,
					},
					legend: {
						display: false,
						position: 'bottom',
						labels: {
							usePointStyle: true,
							padding: 16,
						},
					},
					elements: {
						point: {
							radius: 0,
							backgroundColor: colors.theme['primary'],
						},
						line: {
							tension: 0.4,
							borderWidth: 4,
							borderColor: colors.theme['primary'],
							backgroundColor: colors.transparent,
							borderCapStyle: 'rounded',
						},
						rectangle: {
							backgroundColor: colors.theme['warning'],
						},
						arc: {
							backgroundColor: colors.theme['primary'],
							borderColor: mode == 'dark' ? colors.gray[800] : colors.white,
							borderWidth: 4,
						},
					},
					tooltips: {
						enabled: true,
						mode: 'index',
						intersect: false,
					},
				},
				doughnut: {
					cutoutPercentage: 83,
					legendCallback: function (chart) {
						var data = chart.data;
						var content = '';

						data.labels.forEach(function (label, index) {
							var bgColor = data.datasets[0].backgroundColor[index];

							content += '<span class="chart-legend-item">';
							content +=
								'<i class="chart-legend-indicator" style="background-color: ' + bgColor + '"></i>';
							content += label;
							content += '</span>';
						});

						return content;
					},
				},
			},
		};

		// yAxes
		Chart.scaleService.updateScaleDefaults('linear', {
			gridLines: {
				borderDash: [2],
				borderDashOffset: [2],
				color: mode == 'dark' ? colors.gray[900] : colors.gray[300],
				drawBorder: false,
				drawTicks: false,
				lineWidth: 0,
				zeroLineWidth: 0,
				zeroLineColor: mode == 'dark' ? colors.gray[900] : colors.gray[300],
				zeroLineBorderDash: [2],
				zeroLineBorderDashOffset: [2],
			},
			ticks: {
				beginAtZero: true,
				padding: 10,
				callback: function (value) {
					if (!(value % 10)) {
						return value;
					}
				},
			},
		});

		// xAxes
		Chart.scaleService.updateScaleDefaults('category', {
			gridLines: {
				drawBorder: false,
				drawOnChartArea: false,
				drawTicks: false,
			},
			ticks: {
				padding: 20,
			},
			maxBarThickness: 10,
		});

		return options;
	}

	// Parse global options
	function parseOptions(parent, options) {
		for (var item in options) {
			if (typeof options[item] !== 'object') {
				parent[item] = options[item];
			} else {
				parseOptions(parent[item], options[item]);
			}
		}
	}

	// Push options
	function pushOptions(parent, options) {
		for (var item in options) {
			if (Array.isArray(options[item])) {
				options[item].forEach(function (data) {
					parent[item].push(data);
				});
			} else {
				pushOptions(parent[item], options[item]);
			}
		}
	}

	// Pop options
	function popOptions(parent, options) {
		for (var item in options) {
			if (Array.isArray(options[item])) {
				options[item].forEach(function (data) {
					parent[item].pop();
				});
			} else {
				popOptions(parent[item], options[item]);
			}
		}
	}

	// Toggle options
	function toggleOptions(elem) {
		var options = elem.data('add');
		var $target = $(elem.data('target'));
		var $chart = $target.data('chart');

		if (elem.is(':checked')) {
			// Add options
			pushOptions($chart, options);

			// Update chart
			$chart.update();
		} else {
			// Remove options
			popOptions($chart, options);

			// Update chart
			$chart.update();
		}
	}

	// Update options
	function updateOptions(elem) {
		var options = elem.data('update');
		var $target = $(elem.data('target'));
		var $chart = $target.data('chart');

		// Parse options
		parseOptions($chart, options);

		// Toggle ticks
		toggleTicks(elem, $chart);

		// Update chart
		$chart.update();
	}

	// Toggle ticks
	function toggleTicks(elem, $chart) {
		if (elem.data('prefix') !== undefined || elem.data('prefix') !== undefined) {
			var prefix = elem.data('prefix') ? elem.data('prefix') : '';
			var suffix = elem.data('suffix') ? elem.data('suffix') : '';

			// Update ticks
			$chart.options.scales.yAxes[0].ticks.callback = function (value) {
				if (!(value % 10)) {
					return prefix + value + suffix;
				}
			};

			// Update tooltips
			$chart.options.tooltips.callbacks.label = function (item, data) {
				var label = data.datasets[item.datasetIndex].label || '';
				var yLabel = item.yLabel;
				var content = '';

				if (data.datasets.length > 1) {
					content += '<span class="popover-body-label mr-auto">' + label + '</span>';
				}

				content += '<span class="popover-body-value">' + prefix + yLabel + suffix + '</span>';
				return content;
			};
		}
	}

	// Events

	// Parse global options
	if (window.Chart) {
		parseOptions(Chart, chartOptions());
	}

	// Toggle options
	$toggle.on({
		change: function () {
			var $this = $(this);

			if ($this.is('[data-add]')) {
				toggleOptions($this);
			}
		},
		click: function () {
			var $this = $(this);

			if ($this.is('[data-update]')) {
				updateOptions($this);
			}
		},
	});

	// Return

	return {
		colors: colors,
		fonts: fonts,
		mode: mode,
	};
})();



('use strict');

var BarStackedChart = (function () {
	// Variables

	var $chart = $('#chart-bar-stacked');

	// Methods

	function init($this) {
		// Only for demo purposes - return a random number to generate datasets
		var randomScalingFactor = function () {
			return Math.round(Math.random() * 100);
		};

		// Chart data

		var data = {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [
				{
					label: 'Dataset 1',
					backgroundColor: Charts.colors.theme['danger'],
					data: [
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
					],
				},
				{
					label: 'Dataset 2',
					backgroundColor: Charts.colors.theme['primary'],
					data: [
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
					],
				},
				{
					label: 'Dataset 3',
					backgroundColor: Charts.colors.theme['success'],
					data: [
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
					],
				},
			],
		};

		// Options

		var options = {
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			responsive: true,
			scales: {
				xAxes: [
					{
						stacked: true,
					},
				],
				yAxes: [
					{
						stacked: true,
					},
				],
			},
		};

		// Init chart

		var barStackedChart = new Chart($this, {
			type: 'bar',
			data: data,
			options: options,
		});

		// Save to jQuery object

		$this.data('chart', barStackedChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();

('use strict');

var DoughnutChart = (function () {
	// Variables

	var $chart = $('#chart-doughnut');

	// Methods

	function init($this) {
		var randomScalingFactor = function () {
			return Math.round(Math.random() * 100);
		};

		var doughnutChart = new Chart($this, {
			type: 'doughnut',
			data: {
				labels: ['Danger', 'Warning', 'Success', 'Primary', 'Info'],
				datasets: [
					{
						data: [
							randomScalingFactor(),
							randomScalingFactor(),
							randomScalingFactor(),
							randomScalingFactor(),
							randomScalingFactor(),
						],
						backgroundColor: [
							Charts.colors.theme['danger'],
							Charts.colors.theme['warning'],
							Charts.colors.theme['success'],
							Charts.colors.theme['primary'],
							Charts.colors.theme['info'],
						],
						label: 'Dataset 1',
					},
				],
			},
			options: {
				responsive: true,
				legend: {
					position: 'top',
				},
				animation: {
					animateScale: true,
					animateRotate: true,
				},
			},
		});

		// Save to jQuery object

		$this.data('chart', doughnutChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();

('use strict');

var PieChart = (function () {
	// Variables

	var $chart = $('#chart-pie');

	// Methods

	function init($this) {
		var randomScalingFactor = function () {
			return Math.round(Math.random() * 100);
		};

		var pieChart = new Chart($this, {
			type: 'pie',
			data: {
				labels: ['Danger', 'Warning', 'Success', 'Primary', 'Info'],
				datasets: [
					{
						data: [
							randomScalingFactor(),
							randomScalingFactor(),
							randomScalingFactor(),
							randomScalingFactor(),
							randomScalingFactor(),
						],
						backgroundColor: [
							Charts.colors.theme['danger'],
							Charts.colors.theme['warning'],
							Charts.colors.theme['success'],
							Charts.colors.theme['primary'],
							Charts.colors.theme['info'],
						],
						label: 'Dataset 1',
					},
				],
			},
			options: {
				responsive: true,
				legend: {
					position: 'top',
				},
				animation: {
					animateScale: true,
					animateRotate: true,
				},
			},
		});

		// Save to jQuery object

		$this.data('chart', pieChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();

('use strict');

var PointsChart = (function () {
	// Variables

	var $chart = $('#chart-points');

	// Methods

	function init($this) {
		var salesChart = new Chart($this, {
			type: 'line',
			options: {
				scales: {
					yAxes: [
						{
							gridLines: {
								color: Charts.colors.gray[200],
								zeroLineColor: Charts.colors.gray[200],
							},
							ticks: {},
						},
					],
				},
			},
			data: {
				labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets: [
					{
						label: 'Performance',
						data: [10, 18, 28, 23, 28, 40, 36, 46, 52],
						pointRadius: 10,
						pointHoverRadius: 15,
						showLine: false,
					},
				],
			},
		});

		// Save to jQuery object

		$this.data('chart', salesChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();

('use strict');

var SalesChart = (function () {
	// Variables

	var $chart = $('#chart-sales-dark');

	// Methods

	function init($this) {
		var salesChart = new Chart($this, {
			type: 'line',
			options: {
				scales: {
					yAxes: [
						{
							gridLines: {
								color: Charts.colors.gray[700],
								zeroLineColor: Charts.colors.gray[700],
							},
							ticks: {},
						},
					],
				},
			},
			data: {
				labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets: [
					{
						label: 'Performance',
						data: [0, 20, 10, 30, 15, 40, 20, 60, 60],
					},
				],
			},
		});

		// Save to jQuery object

		$this.data('chart', salesChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();

('use strict');

var SalesChart = (function () {
	// Variables

	var $chart = $('#chart-sales');

	// Methods

	function init($this) {
		var salesChart = new Chart($this, {
			type: 'line',
			options: {
				scales: {
					yAxes: [
						{
							gridLines: {
								color: Charts.colors.gray[200],
								zeroLineColor: Charts.colors.gray[200],
							},
							ticks: {},
						},
					],
				},
			},
			data: {
				labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets: [
					{
						label: 'Performance',
						data: [0, 20, 10, 30, 15, 40, 20, 60, 60],
					},
				],
			},
		});

		// Save to jQuery object

		$this.data('chart', salesChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();

var BarsChart = (function () {
	//
	// Variables
	//

	var $chart = $('#chart-bars');

	//
	// Methods
	//

	// Init chart
	function initChart($chart) {
		// Create chart
		var ordersChart = new Chart($chart, {
			type: 'bar',
			data: {
				labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets: [
					{
						label: 'Sales',
						data: [25, 20, 30, 22, 17, 29],
					},
				],
			},
		});

		// Save to jQuery object
		$chart.data('chart', ordersChart);
	}

	// Init chart
	if ($chart.length) {
		initChart($chart);
	}
})();

('use strict');

var LineChart = (function () {
	// Variables

	var $chart = $('#chart-line');

	// Methods

	function init($this) {
		var salesChart = new Chart($this, {
			type: 'line',
			options: {
				scales: {
					yAxes: [
						{
							gridLines: {
								color: Charts.colors.gray[200],
								zeroLineColor: Charts.colors.gray[200],
							},
							ticks: {},
						},
					],
				},
			},
			data: {
				labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				datasets: [
					{
						label: 'Performance',
						data: [0, 20, 10, 30, 15, 40, 20, 60, 60],
					},
				],
			},
		});

		// Save to jQuery object

		$this.data('chart', salesChart);
	}

	// Events

	if ($chart.length) {
		init($chart);
	}
})();
