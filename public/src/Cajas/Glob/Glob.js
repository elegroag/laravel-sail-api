import { Messages } from '@/Utils';

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
	const modalFilter = new bootstrap.Modal(document.getElementById('filtrar-modal'));
	modalFilter.show();
};

const __readFiltro = () => {
	return {
		campo: $("input[type='hidden'][name='mcampo-filtro[]']").serialize(),
		condi: $("input[type='hidden'][name='mcondi-filtro[]']").serialize(),
		value: $("input[type='hidden'][name='mvalue-filtro[]']").serialize(),
	};
};

const addFiltro = () => {
	const campo = $('#campo-filtro option:selected').text();
	const condi = $('#condi-filtro option:selected').text();
	const value = $('#value-filtro').val();
	const vcampo = $('#campo-filtro').val();
	const vcondi = $('#condi-filtro').val();

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
				<button class='btn btn-outline-danger btn-sm' data-toggle='filter-item-remove'>
					<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>
				</button>
			</td>
		</tr>
		`);

	const html = $('#filtro_add').find('tbody');
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

const buscar = (elem = undefined) => {
	const numero = $('#cantidad_paginate').val();
	let pagina = 1;
	if (elem) {
		pagina = parseInt($(elem).find('a').html());
		if (_.isNaN(pagina) == true) pagina = parseInt($(elem).attr('pagina'));
	}
	if (pagina === 0) return;
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/buscar'),
		data: {
			...__readFiltro(),
			pagina: pagina,
			numero: numero,
		},
		callback: (response) =>  {
			if (response){
				$('#consulta').html(response.consulta);
				$('#paginate').html(response.paginate);
			}else{
				Messages.display(response, 'error');
			}
		}
	});
};

const borrarFiltro = (e) => {
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/borrar_filtro'),
		data: {},
		silent: false,
		callback: (response) => {
			if(response){
				const body = $('#filtro_add').find('tbody');
				body.html("");
				return aplicarFiltro();
			}
		}
	});
};

const aplicarFiltro = () => {
	let cantidad = $('#cantidad_paginate').val();
	if (cantidad === null || cantidad === '') cantidad = 15;

	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/aplicar_filtro'),
		data: {
			...__readFiltro(),
			numero: cantidad,
		},
		callback: (response) => {
			if(response){
				$('#consulta').html(response.consulta);
				$('#paginate').html(response.paginate);
			}else{
				Messages.display(response, 'error');
			}
		},
	});
};

const delFiltro = function (elem) {
	$(elem).parent().parent().remove();
	aplicarFiltro();
};

const changeCantidadPagina = () => {
	const cantidad = $('#cantidad_paginate').val();
	if(cantidad == '' || !cantidad) return false;
	aplicarFiltro();
};

const verArchivo = function (path = void 0, nomarc = void 0) {
	let filename;
	if (path != void 0 && nomarc != void 0) {
		filename = btoa(path + '' + nomarc);
	} else if (path != void 0 && nomarc == void 0) {
		filename = btoa(path);
	} else {
		return;
	}
	$.ajax({
		url: $App.url(window.ServerController + '/principal/download_global'),
		method: 'POST',
		data: {  
			filename 
		},
		xhrFields: {
			responseType: 'blob',
		},
		beforeSend: (xhr) => {
			const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
			xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
		},
		success: (data) => {
			const url = URL.createObjectURL(data);
			window.open(url, filename, 'width=900,height=750,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes');
		},
		error: () => {
			window.App.trigger('alert:error', { message: 'No se pudo cargar el documento' });
		},
	});
};

const openAddress = function (name) {
	if (_.size(_win.Collection.Adress) == 0) {
		$.get(window.App.url('cajas/principal/listaAdress'), 
			function (res) {
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

const validePk = (e) => {
	const target = $(e.currentTarget);
	if (target.val() == '') return;
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController+ '/valide-pk'),
		data: {
			codigo: target.val(),
		},
		silent: true,
		callback: (response) => {
			if(!response) return Messages.display(response, 'error');
			if (response.flag == false) {
				target.val('');
				target.trigger('change');
				target.trigger('focus');
				Messages.display(response.msj, 'warning');
			}
		}
	});
};

const EventsPagination = () => {
	aplicarFiltro();

	const modalFilter = new bootstrap.Modal(document.getElementById('filtrar-modal'));

	//Events DOM Filter
	$(document).on('click', "[data-toggle='reporte']", (e) => {
		e.preventDefault();
		const tipo = $(e.currentTarget).attr('data-type');
		window.location.href = window.App.url(window.ServerController + '/reporte/' + tipo);
	});

	$(document).on('click', "[data-toggle='paginate-buscar']", (e) => {
		e.preventDefault();
		buscar($(e.currentTarget));
	});

	$(document).on('change', "[data-toggle='paginate-change']", changeCantidadPagina);

	//Events DOM Filtros
	$(document).on('click', "[data-toggle='header-filtrar']", (e) => {
		e.preventDefault();
		modalFilter.show();
	});
	$(document).on('click', "[data-toggle='filter-aplicate']", aplicarFiltro);
    $(document).on('click', "[data-toggle='filter-add']", addFiltro);
    $(document).on('click', "[data-toggle='filter-item-remove']", (e) => delFiltro($(e.currentTarget)));
	$(document).on('click', "[data-toggle='filter-remove']", (e) => {
		e.preventDefault();
		borrarFiltro();
	});
};

export {
	EventsPagination,
	buscar,
	openAddress,
	aplicarFiltro,
	borrarFiltro,
	delFiltro,
	changeCantidadPagina,
	addFiltro,
	filtrar,
	actualizar_select,
	validaCaracteres,
	validar,
	validePk,
	verArchivo
};