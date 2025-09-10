import { $App } from '@/App';
import { Messages } from '@/Utils';

let validator;

function galeria() {
	$.ajax({
		type: 'POST',
		url: $App.url('galeria'),
	})
		.done(function (response) {
			let html = '';
			let tmp = _.template(document.getElementById('tmp_galeria').innerHTML);
			$.each(response, function (key, value) {
				html += tmp({ value });
			});
			$('#galeria').html(html);
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
}

$(function () {
	const modalCapture = new bootstrap.Modal(document.getElementById('capture-modal'));

	validator = $('#form').validate({
		rules: {
			archivo: { required: true },
		},
	});

	$(document).on(
		{
			mouseenter: function () {
				$(this)
					.css({
						outline: '0px solid #6EE0FF',
					})
					.stop()
					.animate(
						{
							outlineWidth: '2px',
							outlineColor: '#6EE0FF',
						},
						200,
					);
			},
			mouseleave: function () {
				$(this).stop().animate(
					{
						outlineWidth: '0px',
						outlineColor: '#037736',
					},
					150,
				);
			},
		},
		'.thumbnail',
	);

	galeria();

	$(document).on('click', "[data-toggle='borrar']", (e) => {
		e.preventDefault();
		const tipopc = $(e.currentTarget).attr('data-cid');
		Swal.fire({
			title: 'Esta seguro de borrar?',
			text: '',
			type: 'warning',
			showCancelButton: true,
			confirmButtonClass: 'btn btn-success btn-fill',
			cancelButtonClass: 'btn btn-danger btn-fill',
			confirmButtonText: 'SI',
			cancelButtonText: 'NO',
		}).then((result) => {
			if (result.value) {
				$.ajax({
					type: 'POST',
					url: $App.url('borrar'),
					data: {
						tipopc: tipopc,
					},
				})
					.done(function (response) {
						if (response['flag'] == true) {
							galeria();
							Messages.display(response['msg'], 'success');
						} else {
							Messages.display(response['msg'], 'error');
						}
					})
					.fail(function (jqXHR, textStatus) {
						Messages.display(jqXHR.statusText, 'error');
					});
			}
		});
	});

	$(document).on('click', "[data-toggle='guardar']", (e) => {
		if (!validator.valid()) return;

		$.ajax({
			type: 'POST',
			url: $App.url('guardar'),
			data: new FormData($('#form')[0]),
			processData: false,
			contentType: false,
		})
			.done(function (response) {
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					$('#form :input').each(function (elem) {
						$(this).val('');
						$(this).removeAttr('disabled');
					});
					galeria();
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='arriba']", (e) => {
		const numero = $(e.currentTarget).attr('data-cid');
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: $App.url('arriba'),
			data: {
				numero: numero,
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					galeria();
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='abajo']", (e) => {
		const numero = $(e.currentTarget).attr('data-cid');
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: $App.url('abajo'),
			data: {
				numero: numero,
			},
		})
			.done(function (transport) {
				var response = transport;
				if (response['flag'] == true) {
					Messages.display(response['msg'], 'success');
					galeria();
				} else {
					Messages.display(response['msg'], 'error');
				}
			})
			.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
	});

	$(document).on('click', "[data-toggle='show-modal']", (e) => {
		e.preventDefault();
		let image = $(e.currentTarget).css('background-image');
		image = image.replace('url(', '').replace(')', '').replace(/\"/gi, '');
		$('#img_zoom').attr('src', image);
		modalCapture.show();
	});
});
