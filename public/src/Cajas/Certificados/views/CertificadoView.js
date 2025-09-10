import { RequestListView } from '@/Cajas/RequestListView';
import { $App } from '@/App';

export default class CertificadoView extends RequestListView {
	constructor(options) {
		super({
			...options,
			titulo: 'Listar certificados',
			titulo_detalle: 'Aprobar certificado - ',
		});
	}

	render() {
		const template = _.template(document.getElementById('tmp_table').innerHTML);
		this.$el.html(template());
		this.__beforeRender();
		return this;
	}

	get events() {
		return {
			"click [data-toggle='info']": 'infoDetalle',
			"click [toggle-event='buscar']": 'buscarPagina',
			"change [toggle-event='change']": 'changeCantidad',
			'click #btenviar': 'sendMail',
			"click [data-toggle='file']": 'verArchivo',
		};
	}

	verArchivo(e) {
		e.preventDefault();
		const target = this.$el.find(e.currentTarget);
		const path = target.attr('data-path');
		const nomarc = target.attr('data-file');

		let _filepath;
		if (path != void 0 && nomarc != void 0) {
			_filepath = btoa(path + '' + nomarc);
		} else if (path != void 0 && nomarc == void 0) {
			_filepath = btoa(path);
		} else {
			return;
		}

		const _data = {
			url: $App.kumbiaURL('principal/download_global/' + _filepath),
			filename: _filepath,
		};

		$.ajax({
			type: 'POST',
			url: $App.kumbiaURL('principal/file_existe_global/' + _filepath),
			dataType: 'JSON',
			data: _data,
		}).done((resultado) => {
			if (resultado.success == true) {
				this.__winArchivo(path, nomarc);
			} else {
				$App.trigger('alert:warning', {
					message: 'El archivo no se logra localizar en el servidor',
				});
			}
		});
	}

	__winArchivo(path = '', nomarc = '') {
		const url = ('../' + path + nomarc).replace('//', '/');
		window.open(
			$App.kumbiaURL(url),
			nomarc,
			'width=800, height=750,toobal=no,statusbar=no,scrollbars=yes menuvar=yes',
		);
	}

	sendMail() {
		let nerr = 0;
		let _cedtra = $('#cedtra').val();
		if (_cedtra == '') {
			nerr++;
			document.querySelector('.error_cedtra').innerHTML =
				'<span>El campo cedula es un valor requerido.</span>';
		} else {
			let express = /^([0-9]){8,13}$/;
			if (!express.test(_cedtra.toString())) {
				nerr++;
				document.querySelector('.error_cedtra').innerHTML =
					'<span>La cedula no es un valor valido para continuar.</span>';
			}
		}
		return nerr == 0 ? $('#form_pendiente').submit() : false;
	}
}
