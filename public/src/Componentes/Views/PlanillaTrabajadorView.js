import { ModelView } from '@/Common/ModelView';
import { langDataTable } from '@/Core';

export default class PlanillaTrabajadorView extends ModelView {
	tableView = null;
	constructor(options = {}) {
		super({ ...options, onRender: () => this.afterRender() });
		this.template = _.template(document.getElementById('templatePlanillas').innerHTML);
	}

	afterRender() {
		if (this.model.planilla.length == 0) {
			return;
		}
		this.tableView = this.$el.find('#dataTable').DataTable({
			paging: true,
			ordering: true,
			pageLength: 10,
			info: true,
			searching: true,
			pagingType: 'numbers',
			language: langDataTable,
		});
	}
}
