import { Layout } from '@/Common/Layout';

class LayoutGeneral extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			tagRegions: options.regions || {
				consulta: '#consulta',
				paginate: '#paginate',
				filtro: '#filtro',
			},
		});
	}

	get className() {
		return 'col';
	}
}

export { LayoutGeneral };
