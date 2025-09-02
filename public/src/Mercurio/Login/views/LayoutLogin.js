import { Layout } from '@/Common/Layout';

export default class LayoutLogin extends Layout {
	constructor(options = {}) {
		super({
			...options,
			template: '#tmp_layout',
			tagRegions: options.regions || {
				login: '#render_login',
				recovery: '#render_recovery',
				register: '#render_register',
				verification: '#render_verification',
				info: '#render_info',
			},
		});
	}
}
