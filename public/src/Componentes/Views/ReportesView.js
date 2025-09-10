import { ModelView } from '@/Common/ModelView';
import tpl_reportes_view from './Templates/reportes_view.hbs?raw';
class ReportesView extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(tpl_reportes_view);
	}
}

export { ReportesView };
