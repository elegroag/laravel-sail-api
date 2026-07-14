import { ModelView } from '@/Common/ModelView';
import { langDataTable } from '@/Core';

export default class PlanillaTrabajadorView extends ModelView {
	tableView = null;

	constructor(options = {}) {
		super({ ...options, onRender: () => this.afterRender() });
		this.template = _.template(document.getElementById('templatePlanillas').innerHTML);
	}

	afterRender() {
		if (this.tableView) {
			this.tableView.destroy();
			this.tableView = null;
		}

		if (!this.model.planilla?.length) {
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
			autoWidth: false,
			scrollX: true,
			order: [[1, 'desc']],
			columnDefs: [
				{ targets: [3], className: 'text-end' },
				{ targets: [4], className: 'text-center' },
				{ targets: [6, 7, 8, 9, 10, 11, 12, 13, 14], className: 'text-center' },
			],
			dom:
				'<"consulta-dt-toolbar row align-items-center g-2 mb-3"<"col-md-6"l><"col-md-6"f>>' +
				'rt' +
				'<"consulta-dt-footer row align-items-center g-2 mt-3"<"col-md-6"i><"col-md-6"p>>',
		});
	}

	remove() {
		if (this.tableView) {
			this.tableView.destroy();
			this.tableView = null;
		}

		return super.remove();
	}
}
