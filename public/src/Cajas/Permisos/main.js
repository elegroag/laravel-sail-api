import { $App } from '@/App';
import { Messages } from '@/Utils';

const buscarPermisos = function (tipo = '', buscar = '') {
	const _user = $("[name='usuario']").val();
	$.ajax({
		type: 'POST',
		url: $App.url('buscar'),
		data: {
			usuario: _user,
			tipo: tipo,
			buscar: buscar,
		},
	})
		.done(function (response) {
			if (response.flag == true) {
				$('#permite').html(response.permite);
				$('#nopermite').html(response.nopermite);
			} else {
				Messages.display(response.msg, 'error');
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const guardar = function () {
	if (!$('#form').valid()) {
		return;
	}
	$('#form :input').each(function (elem) {
		$(this).attr('disabled', false);
	});
	$.ajax({
		type: 'POST',
		url: $App.url('guardar'),
		data: $('#form').serialize(),
	})
		.done(function (transport) {
			var response = transport;
			if (response['flag'] == true) {
				buscarPermisos();
				Messages.display(response['msg'], 'success');
				$('#capture-modal').modal('hide');
			} else {
				Messages.display(response['msg'], 'error');
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const eventCheckBox = function (tipo) {
	let checkboxs = '';
	let flag = true;
	if (tipo === 'N') {
		checkboxs = $('#perxN :input');
		flag = $('#selectallN').prop('checked');
	} else if (tipo === 'S') {
		checkboxs = $('#perxS :input');
		flag = $('#selectallS').prop('checked');
	}
	for (let i = 0; i < checkboxs.length; i++) {
		checkboxs[i].checked = flag;
	}
};

const agregar = function () {
	permisos = '';
	checkboxs = $('#nopermite :input');
	for (let i = 1; i < checkboxs.length; i++) {
		if (checkboxs[i].checked == true) {
			permisos += checkboxs[i].id + ';';
		}
	}
	$.ajax({
		type: 'POST',
		url: $App.url('guardar'),
		data: {
			tipo: 'A',
			usuario: $('#usuario').val(),
			permisos: permisos,
		},
	})
		.done(function (transport) {
			var response = transport;
			if (response['flag'] == true) {
				buscarPermisos();
				Messages.display(response['msg'], 'success');
			} else {
				Messages.display(response['msg'], 'error');
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

const quitar = function () {
	permisos = '';
	checkboxs = $('#permite :input');
	for (let i = 1; i < checkboxs.length; i++) {
		if (checkboxs[i].checked == true) {
			permisos += checkboxs[i].id + ';';
		}
	}
	$.ajax({
		type: 'POST',
		url: $App.url('guardar'),
		data: {
			tipo: 'E',
			usuario: $('#usuario').val(),
			permisos: permisos,
		},
	})
		.done(function (transport) {
			var response = transport;
			if (response['flag'] == true) {
				buscarPermisos();
				Messages.display(response['msg'], 'success');
			} else {
				Messages.display(response['msg'], 'error');
			}
		})
		.fail(function (jqXHR, textStatus) {
			Messages.display(jqXHR.statusText, 'error');
		});
};

$(() => {

	$('#usuario').select2();

	$(document).on('change', '#usuario', function (e) {
		e.preventDefault();
		buscarPermisos();
	});

	$(document).on('click', "[toggle-event='eventCheckBox']", (e) => {
		const tipo = $(e.currentTarget).attr('data-tipo');
		eventCheckBox(tipo);
	});

	$(document).on('click', "[toggle-event='agregar']", (e) => {
		e.preventDefault();
		agregar();
	});

	$(document).on('click', "[toggle-event='quitar']", (e) => {
		e.preventDefault();
		quitar();
	});

	$(document).on('click', "[toggle-event='guardar']", (e) => {
		e.preventDefault();
		guardar();
	});

	$(document).on('blur', '#buscarS', (e) => {
		e.preventDefault();
		buscarPermisos('S', $(e.currentTarget).val());
	});

	$(document).on('blur', '#buscarN', (e) => {
		e.preventDefault();
		buscarPermisos('N', $(e.currentTarget).val());
	});
});
