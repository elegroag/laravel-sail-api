import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';
import ErrorHandler from '@/Common/ErrorHandler';
import Logger from '@/Common/Logger';

export default class MoraPresuntaView extends ModelView {
	#errorHandler;
	logger;

	constructor(options = {}) {
		super({
			...options,
			modelDOM: Backbone.Model,
		});

		this.logger = new Logger();
		this.#errorHandler = options.errorHandler || new ErrorHandler();
		this.template = _.template(document.getElementById('tmp_mora_presunta').innerHTML);
	}

	get events() {
		return {
			'click .btn-detalle-cartera': 'onDetalleCartera',
		};
	}

	onDetalleCartera(e) {
		e.preventDefault();
		const $button = this.$(e.currentTarget);
		const $detailRow = $button.closest('tr').next('.detalle-cartera-row');
		const $container = $detailRow.find('.detalle-cartera-container');

		if (!$detailRow.hasClass('d-none') && $button.data('loaded')) {
			$detailRow.addClass('d-none');
			$button.text('Ver detalle');
			return;
		}

		const codsuc = `${$button.data('codsuc')}`;
		const periodo = `${$button.data('periodo')}`;
		$button.prop('disabled', true).text('Consultando...');
		$detailRow.removeClass('d-none');
		$container.html('<div class="text-muted py-2">Consultando detalle...</div>');

		$App.trigger('syncro', {
			url: $App.url('subsidioemp/mora_presunta_detalle'),
			data: { codsuc, periodo },
			callback: (response = {}) => {
				$button.prop('disabled', false);
				if (!response.success) {
					$button.text('Ver detalle');
					$container.html('<div class="text-danger py-2">No fue posible consultar el detalle.</div>');
					return;
				}

				const trabajadores = response.data?.trabajadores || [];
				$container.html(this.#renderDetalle(trabajadores));
				$button.data('loaded', true).text('Ocultar detalle');
			},
			error: (error) => {
				$button.prop('disabled', false).text('Ver detalle');
				$container.html('<div class="text-danger py-2">No fue posible consultar el detalle.</div>');
				this.logger.error('Error consultando detalle de cartera', error);
			},
		});
	}

	#renderDetalle(trabajadores) {
		if (!trabajadores.length) {
			return '<div class="text-muted py-2">No se encontró detalle por trabajador para esta cartera.</div>';
		}

		const rows = trabajadores.map((item) => {
			const valcar = (Number(item.valcar) || 0).toLocaleString('es-CO', {
				minimumFractionDigits: 0,
				maximumFractionDigits: 2,
			});
			return `<tr>
				<td>${_.escape(`${item.coddoc ?? ''}`)}</td>
				<td>${_.escape(`${item.cedtra ?? ''}`)}</td>
				<td>${_.escape(`${item.fullname ?? ''}`)}</td>
				<td class="text-end">${_.escape(valcar)}</td>
			</tr>`;
		}).join('');

		return `<div class="table-responsive py-2">
			<table class="table table-sm table-bordered mb-0">
				<thead>
					<tr>
						<th>Tipo doc.</th>
						<th>Documento</th>
						<th>Trabajador</th>
						<th class="text-end">Valor pendiente</th>
					</tr>
				</thead>
				<tbody>${rows}</tbody>
			</table>
		</div>`;
	}
}
