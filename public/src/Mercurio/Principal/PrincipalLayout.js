import { Layout } from '@/Common/Layout';

class PrincipalLayout extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			tagRegions: options.regions || {
				afiliaciones: '#show_afiliaciones',
				productos: '#show_productos',
				consultas: '#show_consultas',
				totales: '#show_totales',
			},
		});
	}

	get events() {
		return {
			"click [data-toggle='linkFilter']": 'linkFilter',
		};
	}
}

export { PrincipalLayout };
