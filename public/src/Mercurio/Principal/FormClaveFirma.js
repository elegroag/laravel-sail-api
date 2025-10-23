
const ModalClaveFirma = (requireFirma) => {
// Si el backend indicó que falta configurar firma, forzar modal de clave
	if (requireFirma === true) {
		const $modal = $('#modalClaveFirma');
		const modal = new bootstrap.Modal($modal[0], { backdrop: 'static', keyboard: false });
		modal.show({
			backdrop: 'static',
			keyboard: false,
		});
		const tpl = _.template($('#tmp_clave_firma').html());
		$('#modalClaveFirmabody').html(tpl({}));

		// Preparar inputs individuales
		const $inputs = $('#modalClaveFirmabody .digit-input');
		// Auto foco al primer input al mostrar modal
		$modal.on('shown.bs.modal', () => {
			$inputs.first().trigger('focus');
		});
		// Restringir a dígitos y navegación
		$inputs.on('keydown', function (e) {
			const key = e.key;
			const isDigit = /^[0-9]$/.test(key);
			const isBackspace = key === 'Backspace';
			const isArrowLeft = key === 'ArrowLeft';
			const isArrowRight = key === 'ArrowRight';
			if (!(isDigit || isBackspace || isArrowLeft || isArrowRight || key === 'Tab')) {
				e.preventDefault();
				return;
			}
			if (isArrowLeft) {
				$(this).prev('.digit-input').trigger('focus');
				return;
			}
			if (isArrowRight) {
				$(this).next('.digit-input').trigger('focus');
				return;
			}
			if (isBackspace && !this.value) {
				$(this).prev('.digit-input').trigger('focus');
			}
		});
		$inputs.on('input', function () {
			this.value = this.value.replace(/[^0-9]/g, '');
			if (this.value.length === 1) {
				const $next = $(this).next('.digit-input');
				if ($next.length) {
					$next.trigger('focus');
				}
			}
		});

		// Helper para detectar secuencias consecutivas (ascendente o descendente)
		function isConsecutive(code) {
			if (!/^\d{6}$/.test(code)) return false;
			let inc = true, dec = true;
			for (let i = 1; i < code.length; i++) {
				const prev = parseInt(code[i - 1], 10);
				const curr = parseInt(code[i], 10);
				if (curr - prev !== 1) inc = false;
				if (curr - prev !== -1) dec = false;
				if (!inc && !dec) break;
			}
			return inc || dec;
		}

		$('#btnGuardarClaveFirma').off('click').on('click', function (e) {
			e.preventDefault();
			const clave = $inputs.map(function () { return ($(this).val() || '').trim(); }).get().join('');
			const $alert = $('#alertClaveFirma');
			$alert.addClass('d-none').text('');
			if (!/^\d{6}$/.test(clave)) {
				const msj = 'La clave debe contener exactamente 6 dígitos numéricos.';
				window.App.trigger('alert:error', { message: msj });
				$inputs.first().trigger('focus');
				return;
			}
			if (isConsecutive(clave)) {
				const msj = 'La clave no puede ser una secuencia consecutiva.';
				window.App.trigger('alert:error', { message: msj });
				$inputs.first().trigger('focus');
				return;
			}

			window.App.trigger('syncro', {
				url: window.App.url('principal/establecer_clave_firma'),
				data: { clave },
				silent: false,
				callback: (response) => {
					if (response && response.success) {
						window.App.trigger('alert:success', { message: response.msj });
						modal.hide();
						// Redirigir a crear firma si aún no existe
						window.location.href = window.App.url('firmas/index');
					} else {
						const msj = (response && response.msj) || 'No fue posible registrar la clave.';
						window.App.trigger('alert:error', { message: msj });
					}
				},
			});
		});
	};

}

const FormClaveFirma = () => {
	window.App.trigger('syncro', {
		url: window.App.url('principal/require_firma'),
		silent: false,
		callback: (response) => {
			if (response && response.success) {
				ModalClaveFirma(response.requireFirma);
			}
		},
	});
};

export default FormClaveFirma;
