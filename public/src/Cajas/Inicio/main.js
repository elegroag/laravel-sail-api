import { Messages, Utils } from '@/Utils';

$(function () {
	if ($('#permiso_menu').val() == 0)
		Messages.display('El usuario no tiene permiso para esta opci&oacute;n', 'error');

	$(document).on('click', "[data-toggle='action']", (e) => {
		e.preventDefault();
		const url = $(e.currentTarget).attr('data-href');
		window.location.href = Utils.getKumbiaURL(url);
	});
});
