const _handleFiles = function (_file) {
	let fileInput = document.getElementById(_file);
	if (fileInput.files.length == 0) {
		fileInput.value = '';
		$('.custom-file-label').text('Selecionar documento aquí...');
		Swal.fire({
			html: "<p syle='font-size:1rem'>¡Alerta! cargue un archivo por favor.</p>",
			button: 'Cerrar',
		});
		return false;
	}

	let filePath = fileInput.files[0]['name'];
	let _num = 0;
	if (/(\.pdf)$/i.exec(filePath)) _num++;
	if (/(\.jpg)$/i.exec(filePath)) _num++;
	if (/(\.jpeg)$/i.exec(filePath)) _num++;
	if (/(\.png)$/i.exec(filePath)) _num++;
	if (/(\.gif)$/i.exec(filePath)) _num++;
	if (/(\.docx)$/i.exec(filePath)) _num++;
	if (_num == 0) {
		Swal.fire({
			html: "<p syle='font-size:1rem'>¡Alerta! cargue un archivo que tenga extensiones (.pdf) o (.png) o (.jpg) o (.jpeg) o (.docx de Word Office) únicamente.</p>",
			button: 'Cerrar',
		});
		fileInput.value = '';
		$('.custom-file-label').text('Selecionar documento aquí...');
		return false;
	}
	$('.custom-file-label').text(filePath);
	return true;
};

function guardar(codben, nombre) {
	if (_handleFiles('archivo_' + codben) === false) return false;

	var archivo = $('#archivo_' + codben).val();
	var codcer = $('#codcer_' + codben).val();
	if (archivo == '') {
		Swal.fire({
			icon: 'error',
			text: 'Adjunte el archivo',
			button: 'Salir',
		});
		return;
	}
	if (codcer == '') {
		Swal.fire({
			icon: 'error',
			text: 'Seleccionar el certificado',
			button: 'Salir',
		});
		return;
	}
	$('#archivo_' + codben).upload(
		$App.url('guardar'),
		{
			codben: codben,
			nombre: nombre,
			codcer: codcer,
			nomcer: $('#codcer_' + codben + " option[value='" + codcer + "']").text(),
		},
		function (response) {
			if (response['flag'] == true) {
				Swal.fire({
					icon: 'success',
					text: response['msg'],
					button: 'OK',
				});
				setTimeout(function () {
					window.location.reload();
				}, 500);
			} else {
				Swal.fire({
					icon: 'error',
					text: response['msg'],
					button: 'Salir',
				});
			}
		},
	);
}

$(function () {
	$(document).on('change', '.custom-file-input', function (e) {
		let id = $(e.currentTarget).attr('id');
		_handleFiles(id);
	});
});
