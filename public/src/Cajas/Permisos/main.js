import { $App } from '@/App';
import { Messages } from '@/Utils';
import Choices from 'choices.js';
window.App = $App;

const buscarPermisos = function (tipo = '', buscar = '') {
	const _user = $("[name='usuario']").val();
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/buscar'),
		data: {
			usuario: _user,
			tipo: tipo,
			buscar: buscar,
		},
		callback: (response) => {
			if (response.flag == true) {
				$('#permite').html(response.permite);
				$('#nopermite').html(response.nopermite);
			} else {
				Messages.display(response.msg, 'error');
			}
		}
	});
};

const guardar = function () {
	if (!$('#form').valid()) {
		return;
	}
	$('#form :input').each(function (elem) {
		$(this).attr('disabled', false);
	});
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/guardar'),
		data: $('#form').serialize(),
		callback: (response) => {
			if (response.flag == true) {
				buscarPermisos();
				Messages.display(response.msg, 'success');
				$('#capture-modal').modal('hide');
			} else {
				Messages.display(response.msg, 'error');
			}
		}
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
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/guardar'),
		data: {
			tipo: 'A',
			usuario: $('#usuario').val(),
			permisos: permisos,
		},
		callback: (response) => {
			if (response.flag == true) {
				buscarPermisos();
				Messages.display(response.msg, 'success');
			} else {
				Messages.display(response.msg, 'error');
			}
		}
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
	window.App.trigger('syncro', {
		url: window.App.url(window.ServerController + '/guardar'),
		data: {
			tipo: 'E',
			usuario: $('#usuario').val(),
			permisos: permisos,
		},
		callback: (response) => {
			if (response.flag == true) {
				buscarPermisos();
				Messages.display(response.msg, 'success');
			} else {
				Messages.display(response.msg, 'error');
			}
		}
	});
};

$(() => {
	window.App.initialize();
	
	const choicesUser = new Choices(document.querySelector('#usuario'));
	
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
