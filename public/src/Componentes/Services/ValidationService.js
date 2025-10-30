/**
 * Servicio de validación
 * @class ValidationService
 * @description Servicio de validación para aprobacion de las solicitudes
 */
class ValidationService {
	static aplicarFiltro(app, transfer = { callback: void 0, cantidad: 10, tipo: '' }) {
		const { callback = void 0, cantidad = 10, tipo = '' } = transfer;
		const url = tipo !== '' ? 'aplicar_filtro/' + tipo : 'aplicar_filtro/P';

		app.trigger('syncro', {
			url: url,
			data: {
				campo: $("input[type='hidden'][name='mcampo-filtro[]']").serialize(),
				condi: $("input[type='hidden'][name='mcondi-filtro[]']").serialize(),
				value: $("input[type='hidden'][name='mvalue-filtro[]']").serialize(),
				numero: cantidad,
			},
			callback: (response) => {
				return callback(response);
			},
		});
	}

	static cambiarPagina(
		app,
		transfer = { callback: void 0, cantidad: 10, tipo: '', pagina: 1 },
	) {
		const { callback, cantidad, tipo, pagina } = transfer;
		const url = 'change_cantidad_pagina/' + tipo;

		app.trigger('syncro', {
			url: url,
			data: {
				pagina: pagina,
				numero: cantidad,
			},
			callback: (response) => {
				return callback(response);
			},
		});
	}

	static buscarPagina(
		app,
		transfer = { callback: void 0, cantidad: 10, tipo: '', pagina: 0 },
	) {
		const { callback, cantidad, tipo, pagina } = transfer;
		app.trigger('syncro', {
			url: 'buscar/' + tipo,
			data: {
				pagina: pagina,
				numero: cantidad,
			},
			callback: (response) => {
				return callback ? callback(response) : false;
			},
		});
	}

	static devolverSolicitud(
		app,
		transfer = { data: {}, callback: void 0, message_error: '' },
	) {
		const { data, callback, message_error } = transfer;
		app.trigger('syncro', {
			url: 'devolver',
			data: data,
			callback: (response) => {
				if (response) {
					return callback(response);
				}
				Swal.fire({
					title: 'Notificación',
					text:
						message_error != ''
							? message_error
							: 'Error en el procesamiento de datos en el sistema.',
					icon: 'error',
					showConfirmButton: false,
					showCloseButton: true,
					timer: 10000,
				});
				return callback(false);
			},
		});
	}

	static rechazaSolicitud(
		app,
		transfer = { data: {}, callback: void 0, message_error: '' },
	) {
		const { data, callback, message_error } = transfer;
		app.trigger('syncro', {
			url: 'rechazar',
			data: data,
			callback: (response) => {
				if (response) {
					return callback(response);
				}
				Swal.fire({
					title: 'Notificación',
					text:
						message_error != ''
							? message_error
							: 'Error en el procesamiento de datos en el sistema.',
					icon: 'error',
					showConfirmButton: false,
					showCloseButton: true,
					timer: 10000,
				});
				return callback(false);
			},
		});
	}

	static aprobarSolicitud(
		app,
		transfer = { data: {}, callback: void 0, message_error: '' },
	) {
		const { data, callback, message_error } = transfer;
		app.trigger('syncro', {
			url: 'aprueba',
			data: data,
			callback: (response) => {
				if (response) {
					return callback(response);
				}
				Swal.fire({
					title: 'Notificación',
					text:
						message_error != ''
							? message_error
							: 'Error en el procesamiento de datos en el sistema.',
					icon: 'error',
					showConfirmButton: false,
					showCloseButton: true,
					timer: 10000,
				});
				return callback(false);
			},
		});
	}

	static deshacerSolicitud(app, transfer = { data: {}, callback: void 0 }) {
		const { data, callback } = transfer;
		app.trigger('syncro', {
			url: 'deshacer',
			data: data,
			silent: true,
			callback: (response) => {
				return typeof callback === 'function' ? callback(response) : '';
			},
		});
	}
}

export default ValidationService;
