import { $App } from '@/App';
import { Layout } from '@/Common/Layout';
import {
	ConsultaBeneficiarioView,
	ConsultaConyugeView,
	ConsultaTrabajadorView,
} from './ConsultaTrabajadorView';
import { Region } from '@/Common/Region';

class NucleoLayout extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			className: 'tab-content',
			tagRegions: {
				trabajador: '#tabsTrabajador',
				conyuges: '#tabsConyuge',
				beneficiarios: '#tabsBeneficiario',
			},
		});
	}
}

$(() => {
	$App.initialize();
	const layout = new NucleoLayout();
	const region = new Region({ el: '#myTabContent' });
	region.show(layout);

	$App.trigger('syncro', {
		url: $App.url(window.ServerController + '/consulta_nucleo'),
		data: {},
		callback: (response) => {
			if (response && response.success === true) {
				const view = new ConsultaTrabajadorView({
					model: {
						...response.data.params,
						...response.data.trabajador,
					},
				});
				layout.getRegion('trabajador').show(view);

				const viewConyuges = new ConsultaConyugeView({
					model: {
						...response.data.params,
						conyuges: response.data.conyuges,
					},
				});
				layout.getRegion('conyuges').show(viewConyuges);

				const viewBeneficiarios = new ConsultaBeneficiarioView({
					model: {
						...response.data.params,
						beneficiarios: response.data.beneficiarios,
					},
				});
				layout.getRegion('beneficiarios').show(viewBeneficiarios);
			} else {
				$App.trigger('alert:error', { message: response.msj });
			}
		},
		silent: false,
	});
});
