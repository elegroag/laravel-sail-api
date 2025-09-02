import { ModelView } from '@/Common/ModelView';

export default class InfoView extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(document.getElementById('tmp_info').innerHTML);
	}
}
