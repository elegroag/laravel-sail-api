import { aplicarFiltro, borrarFiltro, buscar, openAddress } from './Glob';

$(() => {
	$(document).ajaxSend(function (e, xhr, opt) {
		if (opt.type != 'GET') {
			$('#inProgress').show();
		}
	});

	$(document).ajaxStop(function (e, xhr, opt) {
		$('#inProgress').hide();
		$('[data-toggle="tooltip"]').tooltip();
	});

	$('#inProgress').hide();

	$.validator.addMethod(
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

	$.validator.setDefaults({
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

	$('.modal').on('hidden.bs.modal', function (e) {
		if ($('.modal:visible').length) {
			$('body').addClass('modal-open');
		}
	});

	$(document).on('click', '#button_address_modal', (event) => {
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

	$(document).on('change', '#address_zona', (event) => {
		let valor = $(event.currentTarget).val();
		let lista;
		if (valor === 'R') {
			lista = _.filter(_win.Collection.Adress, function (row) {
				return row.tipo_rural === 'S' || row.tipo_rural === 'V';
			});
			$('#address_barrio').fadeOut();
			$('#show_address_four').fadeOut();
			$('#address_nombre_optional').text('Nombre ubicación');
			$('#show_address_two').attr('class', 'col-md-4');
		} else {
			lista = _.filter(_win.Collection.Adress, function (row) {
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

	$(document).on('click', '#address_one', () => {
		if ($('#address_zona').val() == '') {
			$('#address_zona').trigger('focus');
			$('#address_one').val('');
			$('#address_one-error').text('Selecciona primero la zona de dirección');
			setTimeout(function () {
				$('#address_one-error').text('');
			}, 3000);
		}
	});

	$(document).on('click', '#btOpenAddress', (e) => {
		e.preventDefault();
		openAddress();
	});

	$(document).on('click', '#btAplicarFiltro', (e) => {
		e.preventDefault();
		aplicarFiltro();
	});

	$(document).on('click', '#btBorrarFiltro', (e) => {
		e.preventDefault();
		borrarFiltro();
	});

	$(document).on('click', '#btBuscar', (e) => {
		e.preventDefault();
		buscar();
	});
});
