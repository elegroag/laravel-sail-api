import { $Kumbia, Messages, Utils } from '@/Utils';

const _win = window;
_win.Collection = { Adress: [] };

const validar = function (e) {
	const tecla = document.all ? e.keyCode : e.which;
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
	const patron = /1/; //ver nota
	const te = String.fromCharCode(tecla);
	return patron.test(te);
};

const validaCaracteres = function (element = {}) {
	const id = element.id;
	const caracteres = /[^a-zA-Z\ 0-9]/g;
	if (caracteres.test(element.value)) {
		Messages.display('Caracter Invalido', 'error');
		element.value = '';
	}
};

const download_manual = function (option = '') {
	window.open(Utils.getKumbiaURL('../public/docs/manual_' + option + '.pdf'), 'manuales');
};

const openManuales = () => {
	$('#manuales-dentro-modal').modal();
};

const openHelp = () => {
	$('#help-modal').modal();
};

const actualizar_select = function (name = '') {
	if (name !== undefined && name !== '') {
		$('#' + name).trigger('change');
	} else {
		$('select[select2]').each((index, elem) => {
			$('#' + elem.id).trigger('change');
		});
	}
};

const filtrar = () => {
	$('#filtrar-modal').modal();
};

const __readFiltro = () => {
	return {
		campo: $("input[type='hidden'][name='mcampo-filtro[]']").serialize(),
		condi: $("input[type='hidden'][name='mcondi-filtro[]']").serialize(),
		value: $("input[type='hidden'][name='mvalue-filtro[]']").serialize(),
	};
};

const addFiltro = () => {
	let campo = $('#campo-filtro option:selected').text();
	let condi = $('#condi-filtro option:selected').text();
	let value = $('#value-filtro').val();

	let vcampo = $('#campo-filtro').val();
	let vcondi = $('#condi-filtro').val();

	if ($('#value-filtro').val() == '') return false;
	let _template = _.template(`
		<tr>
			<td>
				<%= campo %>
				<input id='mcampo-filtro[]' name='mcampo-filtro[]' type='hidden' value='<%=vcampo%>'/>
			</td>
			<td>
				<%=condi %>
				<input id='mcondi-filtro[]' name='mcondi-filtro[]'  type='hidden' value='<%=vcondi%>' />
			</td>
			<td>
				<%= value%>
				<input id='mvalue-filtro[]' name='mvalue-filtro[]' type='hidden' value='<%=value%>' />
			</td>
			<td>
				<button class='btn btn-outline-danger btn-sm' toggle-event='remove'>
					<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>
				</button>
			</td>
		</tr>
		`);

	let html = $('#filtro_add').find('tbody');
	html.append(
		_template({
			campo,
			condi,
			value,
			vcampo,
			vcondi,
		}),
	);
};

const buscar = function (elem = undefined) {
	let numero = $('#cantidad_paginate').val();
	let pagina;
	if (elem) {
		pagina = parseInt($(elem).find('a').html());
		if (_.isNaN(pagina) == true) {
			pagina = parseInt($(elem).attr('pagina'));
		}
	} else {
		pagina = 1;
	}

	if (pagina === 0) return;
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/buscar'),
		data: {
			...__readFiltro(),
			pagina: pagina,
			numero: numero,
		},
	})
		.done((response) => {
			$('#consulta').html(response.consulta);
			$('#paginate').html(response.paginate);
		})
		.fail((jqXHR, textStatus) => {
			alert('Request failed: ' + textStatus);
		});
};

const borrarFiltro = (e = '') => {
	$.ajax({
		method: 'GET',
		dataType: 'JSON',
		url: Utils.getKumbiaURL($Kumbia.controller + '/borrarFiltro'),
	}).done(function (response) {
		aplicarFiltro();
	});
};

const aplicarFiltro = (e = '') => {
	let cantidad = $('#cantidad_paginate').val();
	if (cantidad === null || cantidad === '') cantidad = 15;
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/aplicarFiltro'),
		data: {
			...__readFiltro(),
			numero: cantidad,
		},
	})
		.done((response) => {
			$('#consulta').html(response.consulta);
			$('#paginate').html(response.paginate);
		})
		.fail((jqXHR, textStatus) => {
			alert('Request failed: ' + textStatus);
		});
};

const delFiltro = function (elem) {
	$(elem).parent().parent().remove();
	aplicarFiltro();
};

const changeCantidadPagina = (e = '') => {
	let cantidad = $('#cantidad_paginate').val();
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/changeCantidadPagina'),
		data: {
			...__readFiltro(),
			numero: cantidad,
		},
	})
		.done(function (response) {
			$('#consulta').html(response['consulta']);
			$('#paginate').html(response['paginate']);
		})
		.fail(function (jqXHR, textStatus) {
			alert('Request failed: ' + textStatus);
		});
};

const ver_archivo = function (path, nomarc) {
	let url = ('../' + path + nomarc).replace('//', '/');
	window.open(
		Utils.getKumbiaURL(url),
		nomarc,
		'width=800, height=750,toobal=no,statusbar=no,scrollbars=yes menuvar=yes',
	);
};

const verArchivo = function (path = void 0, nomarc = void 0) {
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
			Swal.fire({
				title: 'Notificación',
				text: 'El archivo no se logra localizar en el servidor',
				icon: 'warning',
				showConfirmButton: false,
				timer: 10000,
			});
		}
	});
};

const openAddress = function (name) {
	if (_.size(_win.Collection.Adress) == 0) {
		$.get(Utils.getKumbiaURL('principal/listaAdress'), function (res) {
			let response = JSON.parse(res);
			_win.Collection.Adress = response.data;
			let template = _.template($('#tmp_super_direction').html());
			$('#show_modal_generic').html(
				template({
					adress: _win.Collection.Adress,
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
};

const validePk = (el = '') => {
	if (el === undefined || el === '') el = '#codigo';
	if ($(el).val() == '') return;
	$.ajax({
		type: 'POST',
		url: Utils.getKumbiaURL($Kumbia.controller + '/validePk'),
		data: {
			codigo: $('#codigo').val(),
		},
	})
		.done((response = {}) => {
			if (response.flag == false) {
				$(el).val('');
				$(el).trigger('change');
				$(el).trigger('focus');
				Messages.display(response.msg, 'warning');
			}
		})
		.fail((jqXHR, textStatus) => {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const EventsPagination = () => {
	const modalFilter = new bootstrap.Modal(document.getElementById('filtrar-modal'));

	$(document).on('click', "[data-toggle='reporte']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-type');
		window.location.href = Utils.getKumbiaURL($Kumbia.controller + '/reporte/' + tipo);
	});

	$(document).on('click', "[data-toggle='filtrar']", (e) => {
		e.preventDefault();
		modalFilter.show();
	});

	$(document).on('click', "[data-toggle='page-buscar']", (e) => {
		e.preventDefault();
		buscar($(e.currentTarget));
	});

	$(document).on('change', "[data-toggle='page-change']", (e) => {
		changeCantidadPagina();
	});

	aplicarFiltro();
};

export {
	EventsPagination,
	buscar,
	openAddress,
	openHelp,
	download_manual,
	aplicarFiltro,
	borrarFiltro,
	delFiltro,
	changeCantidadPagina,
	verArchivo,
	addFiltro,
	filtrar,
	actualizar_select,
	openManuales,
	validaCaracteres,
	validar,
	validePk,
};
