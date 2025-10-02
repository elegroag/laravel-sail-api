import { $App } from '@/App';

class TotalesView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.tableView = undefined;
    }

    get className() {
        return 'row justify-content-between mb-3';
    }

    initialize() {
        this.template = document.getElementById('tmp_totales').innerHTML;
    }

    render() {
        const template = _.template(this.template);
        this.$el.html(template(this.model));
        return this;
    }

    get events() {
        return {};
    }

    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { TotalesView };
