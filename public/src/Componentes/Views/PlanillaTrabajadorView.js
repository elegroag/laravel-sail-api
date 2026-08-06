import { ModelView } from '@/Common/ModelView';

export default class PlanillaTrabajadorView extends ModelView {
	constructor(options = {}) {
		super({ ...options, onRender: () => this.afterRender() });
		this.template = _.template(document.getElementById('templatePlanillas').innerHTML);
		this.pageLength = 10;
		this.currentPage = 1;
		this.searchTerm = '';
		this._onSearch = null;
		this._onPageLength = null;
		this._onPaginationClick = null;
	}

	afterRender() {
		if (!this.$el.find('#consultaList').length) {
			return;
		}

		this.initListView();
	}

	initListView() {
		this.unbindListEvents();

		this.pageLength = Number(this.$el.find('#consultaPageLength').val() || 10);
		this.currentPage = 1;
		this.searchTerm = '';

		this._onSearch = (event) => {
			this.searchTerm = String(event.target.value || '')
				.trim()
				.toLowerCase();
			this.currentPage = 1;
			this.renderListPage();
		};

		this._onPageLength = (event) => {
			this.pageLength = Number(event.target.value || 10);
			this.currentPage = 1;
			this.renderListPage();
		};

		this._onPaginationClick = (event) => {
			const button = event.target.closest('[data-page]');
			if (!button || button.disabled) {
				return;
			}

			const page = Number(button.getAttribute('data-page'));
			if (!Number.isFinite(page) || page < 1) {
				return;
			}

			this.currentPage = page;
			this.renderListPage();
		};

		this.$el.find('#consultaSearch').on('input', this._onSearch);
		this.$el.find('#consultaPageLength').on('change', this._onPageLength);
		this.$el.find('#consultaListPagination').on('click', this._onPaginationClick);

		this.renderListPage();
	}

	getFilteredItems() {
		const items = Array.from(this.$el.find('#consultaList .consulta-list-item'));
		if (!this.searchTerm) {
			return items;
		}

		return items.filter((item) => {
			const haystack = String(item.getAttribute('data-search') || '');
			return haystack.includes(this.searchTerm);
		});
	}

	renderListPage() {
		const items = Array.from(this.$el.find('#consultaList .consulta-list-item'));
		const filtered = this.getFilteredItems();
		const total = filtered.length;
		const totalPages = Math.max(1, Math.ceil(total / this.pageLength));

		if (this.currentPage > totalPages) {
			this.currentPage = totalPages;
		}

		const start = (this.currentPage - 1) * this.pageLength;
		const end = start + this.pageLength;
		const visibleSet = new Set(filtered.slice(start, end));

		items.forEach((item) => {
			item.hidden = !visibleSet.has(item);
		});

		const info = this.$el.find('#consultaListInfo');
		if (!total) {
			info.text('No hay registros que coincidan con la búsqueda');
		} else {
			const from = start + 1;
			const to = Math.min(end, total);
			info.text(`Mostrando ${from} a ${to} de ${total} registros`);
		}

		this.renderPagination(totalPages);
	}

	renderPagination(totalPages) {
		const container = this.$el.find('#consultaListPagination');
		container.empty();

		if (totalPages <= 1) {
			return;
		}

		const addButton = (label, page, options = {}) => {
			const button = document.createElement('button');
			button.type = 'button';
			button.textContent = label;
			button.setAttribute('data-page', String(page));
			if (options.active) {
				button.classList.add('is-active');
			}
			if (options.disabled) {
				button.disabled = true;
			}
			container.append(button);
		};

		addButton('‹', this.currentPage - 1, { disabled: this.currentPage <= 1 });

		const windowSize = 5;
		let from = Math.max(1, this.currentPage - Math.floor(windowSize / 2));
		let to = Math.min(totalPages, from + windowSize - 1);
		from = Math.max(1, to - windowSize + 1);

		for (let page = from; page <= to; page += 1) {
			addButton(String(page), page, { active: page === this.currentPage });
		}

		addButton('›', this.currentPage + 1, { disabled: this.currentPage >= totalPages });
	}

	unbindListEvents() {
		if (this._onSearch) {
			this.$el.find('#consultaSearch').off('input', this._onSearch);
		}
		if (this._onPageLength) {
			this.$el.find('#consultaPageLength').off('change', this._onPageLength);
		}
		if (this._onPaginationClick) {
			this.$el.find('#consultaListPagination').off('click', this._onPaginationClick);
		}
	}

	remove() {
		this.unbindListEvents();
		return super.remove();
	}
}
