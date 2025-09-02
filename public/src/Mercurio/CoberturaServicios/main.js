import { $App } from '@/App';

if (CODSER === undefined || CODSER === void 0 || CODSER === '') {
	window.location.href = $App.url('principal/index');
}

const validaCupos = function () {
	$.get($App.url('numeroCuposDisponibles/' + CODSER))
		.done(function (response) {
			$('#showNumCupos').text(response.cupos);
		})
		.fail(function (err) {
			console.log(err.responseText);
			return false;
		});
};

const loadData = function (beneficiarios) {
	let tagRegistros = $('#showRegistros');
	let template = _.template($('#tmp_registros').html());
	let html = '';
	if (_.size(beneficiarios) == 0) {
		tagRegistros.html(
			"<tr><td colspan='4'>No hay beneficiarios aptos para aplicar al producto.</td></tr>",
		);
	} else {
		_.each(beneficiarios, function (benefi) {
			html += template(benefi);
		});
		tagRegistros.html(html);
	}
};

const buscarAplicados = function () {
	$.get($App.url('serviciosAplicados/' + CODSER))
		.done(function (response) {
			if (response.success) {
				if (_.size(response.data) > 0) {
					$('#show_access_davivienda').fadeIn('fast');
				}
				loadData(response.data);
			}
		})
		.fail(function (err) {
			console.log(err.responseText);
			return false;
		});
};

$(() => {
	$App.initialize();

	buscarAplicados();

	setInterval(function () {
		validaCupos();
	}, 10000);

	$(document).on('click', 'button[toggle="aplica"]', (event) => {
		event.preventDefault();
		var target = $(event.currentTarget);
		Swal.fire({
			title: '¡Confirmar!',
			html: "<p style='font-size:0.97rem'>¿Está seguro que desea aplicar al complemento nutricional?</p>",
			showCancelButton: true,
			confirmButtonClass: 'btn btn-sm btn-success',
			cancelButtonClass: 'btn btn-sm btn-danger',
			confirmButtonText: 'SI',
			cancelButtonText: 'NO',
		}).then(function (result) {
			if (result.value) {
				let _token = {
					id: target.attr('data-cid'),
					docben: target.attr('data-docu'),
					codser: CODSER,
				};
				$.ajax({
					method: 'POST',
					url: $App.url('aplicarCupo'),
					dataType: 'JSON',
					cache: false,
					data: _token,
				})
					.done(function (response) {
						if (response.success) {
							loadData(response.beneficiarios);
							validaCupos();
							Swal.fire({
								title: 'Notificación OK',
								html:
									"<p class='text-left' style='font-size:1rem'>" + response.msj + '</p>',
								showCloseButton: false,
								showConfirmButton: true,
								allowOutsideClick: false,
								allowEscapeKey: false,
								confirmButtonText: 'Continuar',
							}).then(function (e) {
								if (e.value === true) {
									setTimeout(function () {
										window.location.href = '#';
									}, 100);
								}
							});
						} else {
							if (response.beneficiarios) {
								loadData(response.beneficiarios);
							}
							Swal.fire({
								title: 'Notificación Alerta',
								text: response.msj,
								icon: 'warning',
								showConfirmButton: false,
								showCloseButton: true,
								timer: 10000,
							});
						}
					})
					.fail(function (err) {
						console.log(err.responseText);
						return false;
					});
			}
		});
	});

	$(document).on('click', 'button[toggle="buscar"]', (event) => {
		event.preventDefault();
		var target = $(event.currentTarget);
		let _token = {
			id: target.attr('data-cid'),
			docben: target.attr('data-docu'),
			codser: CODSER,
		};
		$.ajax({
			method: 'POST',
			url: $App.url('buscarCupo'),
			dataType: 'JSON',
			cache: false,
			data: _token,
		})
			.done(function (response) {
				if (response.success) {
					Swal.fire({
						title: 'Nota',
						html: "<p class='text-left' style='font-size:1rem'>" + response.msj + '</p>',
						showCloseButton: false,
						showConfirmButton: true,
						allowOutsideClick: false,
						allowEscapeKey: false,
						confirmButtonText: 'Continuar',
					}).then(function (e) {
						if (e.value === true) {
							setTimeout(function () {
								window.location.href = '#';
							}, 100);
						}
					});
				}
			})
			.fail(function (err) {
				console.log(err.responseText);
				return false;
			});
	});

	$(document).on('click', 'button[toggle="copy"]', (event) => {
		event.preventDefault();
		let target = $(event.currentTarget);
		let pin = target.attr('data-cid');

		let copyFrom = document.createElement('textarea');
		copyFrom.textContent = pin;
		let body = document.getElementsByTagName('body')[0];
		body.appendChild(copyFrom);
		copyFrom.select();
		document.execCommand('copy');
		body.removeChild(copyFrom);
	});
});
