import { ModelView } from '@/Common/ModelView';

export default class NoGiroView extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(document.getElementById('templateNoGiro').innerHTML);
	}
}
