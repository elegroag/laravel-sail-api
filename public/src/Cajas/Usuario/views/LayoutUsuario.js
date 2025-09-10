import { Layout } from '@/Common/Layout';

class LayoutUsuario extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			tagRegions: options.regions || {
				header: '#header',
				subheader: '#subheader',
				body: '#body',
			},
		});
	}
}

export { LayoutUsuario };
