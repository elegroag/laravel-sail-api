import { ModelView } from '@/Common/ModelView';

class BreadcrumbView extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(document.getElementById('tmp_breadcrumb').innerHTML);
    }

    renderBreadcrumb(items = []) {
        const current = document.getElementById('breadcrumb-current');
        if (current) {
            const title = window.BREADCRUMB_TITLE || (items.length > 0 ? items[items.length - 1].title : '');
            current.textContent = title;
        }
        return this;
    }

    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { BreadcrumbView };