import { ModelView } from '@/Common/ModelView';

class BreadcrumbView extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(document.getElementById('tmp_breadcrumb').innerHTML);
    }

    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { BreadcrumbView };