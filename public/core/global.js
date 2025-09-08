window.Collection = {
	Adress: [],
};

$(function () {
	$('#inProgress').hide();

	jQuery.validator.addMethod(
		'greaterThan',
		function (value, element, params) {
			if ($(params[0]).val() != '') {
				if (!/Invalid|NaN/.test(new Date(value))) {
					return new Date(value) >= new Date($(params[0]).val());
				}
				return (
					(isNaN(value) && isNaN($(params[0]).val())) ||
					Number(value) > Number($(params[0]).val())
				);
			}
			return true;
		},
		'Debe ser mayor a {1}.',
	);

	jQuery.validator.setDefaults({
		validClass: 'is-valid',
		errorClass: 'is-invalid',
		highlight: function (element, errorClass, validClass) {
			var elem = $(element);
			if (elem.hasClass('select2-hidden-accessible')) {
				$('#select2-' + elem.attr('id') + '-container')
					.parent()
					.addClass(errorClass)
					.removeClass(validClass);
			} else if (element.type === 'radio') {
				this.findByName(element.name).addClass(errorClass).removeClass(validClass);
			} else {
				elem.addClass(errorClass).removeClass(validClass);
			}
		},
		unhighlight: function (element, errorClass, validClass) {
			var elem = $(element);
			if (elem.hasClass('select2-hidden-accessible')) {
				$('#select2-' + elem.attr('id') + '-container')
					.parent()
					.removeClass(errorClass)
					.addClass(validClass);
			} else if (element.type === 'radio') {
				this.findByName(element.name).removeClass(errorClass).addClass(validClass);
			} else {
				elem.removeClass(errorClass).addClass(validClass);
			}
		},
		errorPlacement: function (error, element) {
			var elem = $(element);
			if (elem.hasClass('select2-hidden-accessible')) {
				element = $('#select2-' + elem.attr('id') + '-container').parent();
				error.insertAfter(element);
			} else {
				error.insertAfter(element);
			}
		},
	});

	$("input[date='date']").datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true,
	});

	$("input[date='month']").datepicker({
		format: 'yyyymm',
		startView: 'months',
		minViewMode: 'months',
		autoclose: true,
	});

	$('select[select2]').each(function (index, elem) {
		$('#' + elem.id).select2({
			dropdownParent: $('#' + elem.id).parent(),
		});
	});

	/* 	if (window.typehead != undefined) {
		$.typeahead({
			input: '.js-typeahead',
			minLength: 2,
			maxItem: 8,
			maxItemPerGroup: 6,
			order: 'asc',
			cancelButton: false,
			group: {
				key: 'division',
				template: function (item) {
					var division = item.division;
					return division;
				},
			},
			display: ['detalle', 'nota', 'division'],
			template:
				'<span>' +
				'<span class="detalle">{{detalle}}</span>' +
				'<span class="nota">({{nota}})</span>' +
				'</span>',
			correlativeTemplate: true,
			source: {
				href: '{{url}}',
				data: typehead,
			},
		});
	} */

	$('.modal').on('hidden.bs.modal', function (e) {
		if ($('.modal:visible').length) {
			$('body').addClass('modal-open');
		}
	});

	$(document).on('click', '#button_address_modal', function (event) {
		event.preventDefault();
		let barrio = '';
		let address;

		if ($('#address_five').val() !== '') {
			barrio = ' BRR ' + $('#address_five').val();
		}
		if ($('#address_one').val() == null && $('#address_two').val() == '') {
			address = 'NINGUNA';
		} else {
			address =
				$('#address_one').val() +
				' ' +
				$('#address_two').val() +
				' ' +
				$('#address_four').val() +
				' ' +
				barrio;
		}
		let tagname = $('#tagname').val();
		$('#' + tagname).val(address);
		$('#modal_generic').modal('hide');
	});

	$(document).on('change', '#address_zona', function (event) {
		let valor = $(event.currentTarget).val();
		let lista;
		if (valor === 'R') {
			lista = _.filter(window.Collection.Adress, function (row) {
				return row.tipo_rural === 'S' || row.tipo_rural === 'V';
			});
			$('#address_barrio').fadeOut();
			$('#show_address_four').fadeOut();
			$('#address_nombre_optional').text('Nombre ubicación');
			$('#show_address_two').attr('class', 'col-md-4');
		} else {
			lista = _.filter(window.Collection.Adress, function (row) {
				return row.tipo_rural === 'N';
			});
			$('#show_address_four').fadeIn();
			$('#address_barrio').fadeIn();
			$('#address_nombre_optional').text('Número ');
			$('#show_address_two').attr('class', 'col-md-2');
		}
		let html = '';
		let template = _.template(`<option value="<%=estado%>"><%=detalle%></option>`);
		_.each(lista, function (adres) {
			html += template(adres);
		});
		$('#address_one').html(html);
	});

	$(document).on('click', '#address_one', function (event) {
		if ($('#address_zona').val() == '') {
			$('#address_zona').focus();
			$('#address_one').val('');
			$('#address_one-error').text('Selecciona primero la zona de dirección');
			setTimeout(function () {
				$('#address_one-error').text('');
			}, 3000);
		}
	});
});

$(document).ajaxSend(function (e, xhr, opt) {
	if (opt.type != 'GET') {
		$('#inProgress').show();
	}
});

$(document).ajaxStop(function (e, xhr, opt) {
	$('#inProgress').hide();
	$('[data-toggle="tooltip"]').tooltip();
});

function actualizar_select(name) {
	if (name != undefined) {
		$('#' + name).trigger('change');
	} else {
		$('select[select2]').each(function (index, elem) {
			$('#' + elem.id).trigger('change');
		});
	}
}

function openHelp() {
	$('#help-modal').modal();
}

function buscar(elem) {
	cantidad = $('#cantidad_paginate').val();
	var pagina = parseInt($(elem).find('a').html());
	if (isNaN(pagina) == true) {
		var pagina = $(elem).attr('pagina');
	}
	if (parseInt(pagina) == 0) return;
	var request = $.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/buscar'),
		data: {
			pagina: pagina,
		},
	});
	request.done(function (transport) {
		var response = jQuery.parseJSON(transport);
		$('#consulta').html(response['consulta']);
		$('#paginate').html(response['paginate']);
		$('#cantidad_paginate').val(cantidad);
	});
	request.fail(function (jqXHR, textStatus) {
		alert('Request failed: ' + textStatus);
	});
}

function filtrar() {
	$('#filtrar-modal').modal();
}

function addFiltro() {
	var html = $('#filtro_add').find('tbody').html();
	html += '<tr>';
	html +=
		'<td>' +
		$('#campo-filtro option:selected').text() +
		"<input id='mcampo-filtro[]' name='mcampo-filtro[]' type='hidden' value='" +
		$('#campo-filtro').val() +
		"' /></td>";
	html +=
		'<td>' +
		$('#condi-filtro option:selected').text() +
		"<input id='mcondi-filtro[]' name='mcondi-filtro[]'  type='hidden' value='" +
		$('#condi-filtro').val() +
		"' /></td>";
	html +=
		'<td>' +
		$('#value-filtro').val() +
		"<input id='mvalue-filtro[]' name='mvalue-filtro[]' type='hidden' value='" +
		$('#value-filtro').val() +
		"'></td>";
	html +=
		"<td><button class='btn btn-outline-secondary btn-sm' onclick='delFiltro(this);'><span class='btn-inner--icon'><i class='fas fa-times'></i></span></button></td>";
	html += '</tr>';
	$('#filtro_add').find('tbody').html(html);
}

function borrarFiltro() {
	$.ajax({
		method: 'GET',
		dataType: 'JSON',
		url: Utils.getKumbiaURL($Kumbia.controller + '/borrarFiltro'),
	}).done(function (response) {
		aplicarFiltro();
		console.log(response);
	});
}

function delFiltro(elem) {
	$(elem).parent().parent().remove();
}

function aplicarFiltro() {
	cantidad = $('#cantidad_paginate').val();
	if (cantidad == null) cantidad = 5;
	var request = $.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/aplicarFiltro'),
		data: {
			campo: $("input[type='hidden'][name='mcampo-filtro[]']").serialize(),
			condi: $("input[type='hidden'][name='mcondi-filtro[]']").serialize(),
			value: $("input[type='hidden'][name='mvalue-filtro[]']").serialize(),
		},
	});
	request.done(function (transport) {
		var response = jQuery.parseJSON(transport);
		$('#consulta').html(response['consulta']);
		$('#paginate').html(response['paginate']);
		$('#cantidad_paginate').val(cantidad);
	});
	request.fail(function (jqXHR, textStatus) {
		alert('Request failed: ' + textStatus);
	});
}

function changeCantidadPagina() {
	cantidad = $('#cantidad_paginate').val();
	var request = $.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/changeCantidadPagina'),
		data: {
			numero: cantidad,
		},
	});
	request.done(function (transport) {
		var response = jQuery.parseJSON(transport);
		$('#consulta').html(response['consulta']);
		$('#paginate').html(response['paginate']);
		$('#cantidad_paginate').val(cantidad);
	});
	request.fail(function (jqXHR, textStatus) {
		alert('Request failed: ' + textStatus);
	});
}

function ver_archivo(path, nomarc) {
	let url = ('../' + path + nomarc).replace('//', '/');
	window.open(
		Utils.getKumbiaURL(url),
		nomarc,
		'width=800, height=750,toobal=no,statusbar=no,scrollbars=yes menuvar=yes',
	);
}

function verArchivo(path = void 0, nomarc = void 0) {
	let _filepath;
	if (path != void 0 && nomarc != void 0) {
		_filepath = btoa(path + '' + nomarc);
	} else if (path != void 0 && nomarc == void 0) {
		_filepath = btoa(path);
	} else {
		return;
	}
	let _data = {
		url: Utils.getKumbiaURL('principal/download_global/' + _filepath),
		filename: _filepath,
	};
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL('principal/file_existe_global/' + _filepath),
		dataType: 'JSON',
	}).done(function (resultado) {
		if (resultado.success) {
			ver_archivo(path, nomarc);
		} else {
			swal.fire({
				title: 'Notificación',
				text: 'El archivo no se logra localizar en el servidor',
				icon: 'warning',
				showConfirmButton: false,
				timer: 10000,
			});
		}
	});
}

function openAddress(name) {
	if (_.size(window.Collection.Adress) == 0) {
		$.get(Utils.getKumbiaURL('principal/listaAdress'), function (res) {
			let response = JSON.parse(res);
			window.Collection.Adress = response.data;
			let template = _.template($('#tmp_super_direction').html());
			$('#show_modal_generic').html(
				template({
					adress: window.Collection.Adress,
				}),
			);
			$('#modal_generic').modal();
			$('#size_modal_generic').addClass('modal-lg');
			$('#tagname').val(name);
			$('#button_address_modal').unbind('click');
			$('#form_address_modal :input').each(function (elem) {
				$(this).val('');
			});
		});
	} else {
		$('#modal_generic').modal();
		$('#size_modal_generic').addClass('modal-lg');
		$('#tagname').val(name);
		$('#button_address_modal').unbind('click');
		$('#form_address_modal :input').each(function (elem) {
			$(this).val('');
		});
	}
}

function validar(e) {
	tecla = document.all ? e.keyCode : e.which;
	if (tecla == 8) return true; //Tecla de retroceso (para poder borrar)
	if (tecla == 44) return true; //Coma ( En este caso para diferenciar los decimales )
	if (tecla == 48) return true;
	if (tecla == 49) return true;
	if (tecla == 50) return true;
	if (tecla == 51) return true;
	if (tecla == 52) return true;
	if (tecla == 53) return true;
	if (tecla == 54) return true;
	if (tecla == 55) return true;
	if (tecla == 56) return true;
	patron = /1/; //ver nota
	te = String.fromCharCode(tecla);
	return patron.test(te);
}

function openManuales() {
	$('#manuales-dentro-modal').modal();
}

function download_manual(option) {
	window.open(Utils.getKumbiaURL('../public/docs/manual_' + option + '.pdf'), 'manuales');
}

function validaCaracteres(element) {
	id = element.id;
	var caracteres = /[^a-zA-Z\ 0-9]/g;
	if (caracteres.test(element.value)) {
		Messages.display('Caracter Invalido', 'error');
		element.value = '';
	}
}

var dataTableConfig = {
	processing: 'Procesando...',
	lengthMenu: 'Mostrar _MENU_ resultados por pagínas',
	zeroRecords: 'No se encontraron resultados',
	info: 'Mostrando pagína _PAGE_ de _PAGES_',
	infoEmpty: 'No records available',
	infoFiltered: '(filtered from _MAX_ total records)',
	emptyTable: 'Ningún dato disponible en esta tabla',
	search: 'Buscar',
	paginate: {
		next: '>>',
		previus: '<<',
		first: 'Primero',
		last: 'Ultimo',
		previous: '<<',
	},
	loadingRecords: 'Cargando...',
	buttons: {
		copy: 'Copiar',
		colvis: 'Visibilidad',
		collection: 'Colección',
		colvisRestore: 'Restaurar visibilidad',
		copyKeys:
			'Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.',
		copySuccess: {
			1: 'Copiada 1 fila al portapapeles',
			_: 'Copiadas %d fila al portapapeles',
		},
	},
};
