import { $App } from '@/App';

class ServiciosView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.tableView = undefined;
    }

    get className() {
        return 'col-xs-12 col-lg-3';
    }

    initialize() {
        this.template = document.getElementById('tmp_card').innerHTML;
    }

    render() {
        const template = _.template(this.template);
        const path = $("[name='csrf-token']").attr('path');

        this.model.url = $App.kumbiaURL(this.model.url);
        this.model.imagen = path + '/img/Mercurio/' + this.model.imagen;
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

export { ServiciosView };
