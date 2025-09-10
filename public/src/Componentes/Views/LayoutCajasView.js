import { Layout } from '@/Common/Layout';

class LayoutCajasView extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			tagRegions: {
				header: '#header_group_button',
				subheader: '#render_subheader',
				body: '#app',
			},
		});
	}
}

export { LayoutCajasView };
