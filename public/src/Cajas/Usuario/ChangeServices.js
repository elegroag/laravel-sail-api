const cambioEmail = function () {
	var validator = $('#form').validate({
		rules: {
			email: { required: true, email: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	Swal.fire({
		title: 'Esta seguro de actualizar el email de aviso?',
		text: '',
		type: 'warning',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success btn-fill',
		cancelButtonClass: 'btn btn-danger btn-fill',
		confirmButtonText: 'SI',
		cancelButtonText: 'NO',
	}).then((result) => {
		if (result.value) {
			var request = $.ajax({
				type: 'POST',
				url: window.App.url('cambio_email'),
				data: {
					email: $('#email').val(),
				},
			});
			request.done(function (transport) {
				var response = jQuery.parseJSON(transport);
				if (response['flag'] == false) {
					Messages.display(response['msg'], 'error');
				} else {
					Messages.display(response['msg'], 'success');
				}
			});
			request.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
		}
	});
};

const cambioClave = function () {
	var validator = $('#form').validate({
		rules: {
			claant: { required: true },
			clave: { required: true },
			clacon: { required: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	Swal.fire({
		title: 'Esta seguro de cambiar la clave de acceso?',
		text: '',
		type: 'warning',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success btn-fill',
		cancelButtonClass: 'btn btn-danger btn-fill',
		confirmButtonText: 'SI',
		cancelButtonText: 'NO',
	}).then((result) => {
		if (result.value) {
			var request = $.ajax({
				type: 'POST',
				url: window.App.url('cambio_clave'),
				data: {
					claant: $('#claant').val(),
					clave: $('#clave').val(),
					clacon: $('#clacon').val(),
				},
			});
			request.done(function (transport) {
				var response = jQuery.parseJSON(transport);
				if (response['flag'] == false) {
					Messages.display(response['msg'], 'error');
				} else {
					Messages.display(response['msg'], 'success');
				}
			});
			request.fail(function (jqXHR, textStatus) {
				alert('Request failed: ' + textStatus);
			});
		}
	});
};

export { cambioClave, cambioEmail };
