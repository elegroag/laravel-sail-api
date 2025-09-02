'use strict';
import { $App } from '@/App';

class GestionAdjuntoService {
	constructor() {}

	borrarArchivo(transfer = {}) {
		const { data, callback } = transfer;
		$App.trigger('syncro', {
			url: $App.url('borrarArchivo'),
			data,
			callback: (response) => {
				if (response) {
					if (response.success) {
						$App.trigger('alert:success', { message: response.msj });
						return callback(response);
					} else {
						$App.trigger('alert:error', { message: response.msj });
					}
				}
				return callback(false);
			},
		});
	}

	processDocument(transfer = {}) {
		const { data, url, callback } = transfer;
		$App.trigger('syncro', {
			url: url,
			data: data,
			callback: (response) => {
				if (response) {
					if (response.success) {
						return callback(response);
					} else {
						$App.trigger('alert:warning', {
							message: 'No se puede generar el formulario',
						});
					}
				}
				return callback(false);
			},
		});
	}

	guardarArchivo(transfer = {}) {
		const { target, id, coddoc, callback } = transfer;
		const formData = new FormData();

		formData.append('archivo_' + id + '_' + coddoc, $(target)[0].files[0]);
		formData.append('id', id);
		formData.append('coddoc', coddoc);

		$App.trigger('upload', {
			url: $App.url('guardarArchivo'),
			data: formData,
			callback: (response) => {
				if (response) {
					if (response.success) {
						return callback(response);
					} else {
						$App.trigger('alert:error', { message: response['msj'] });
					}
				}
				return callback(false);
			},
		});
	}
}

export { GestionAdjuntoService };
