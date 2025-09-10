import { Layout } from '@/Common/Layout';

export default class LayoutTrabajador extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout_trabajador',
			tagRegions: {
				trabajador: '#show_trabajador',
				trayectorias: '#show_trayectoria',
				salarios: '#show_salario',
			},
		});
	}
}
