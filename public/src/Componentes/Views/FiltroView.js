import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';

class FiltroView extends ModelView {
    constructor(options) {
        super(options);
        this.template = _.template(document.getElementById('tmp_filtro').innerHTML);
    }

    get className() {
        return 'col';
    }

    get events() {
        return {
            "click [toggle-event='volver']": 'volverLista',
            "click [data-toggle='filter-close']": 'volverLista',
            "click [data-toggle='filter-add']": 'addFiltro',
            "click [data-toggle='filter-remove']": 'borrarFiltro',
            "click [data-toggle='filter-item-remove']": 'removeFiltro',
            "click [data-toggle='filter-aplicate']": 'aplicaFiltro',
            "click [toggle-event='remove']": 'removeFiltro',
        };
    }

    volverLista(e) {
        e.preventDefault();
        this.trigger('load:volver', {});
    }

    addFiltro(e) {
        e.preventDefault();
        this.__addFiltro();
    }

    borrarFiltro(e) {
        e.preventDefault();
        $App.trigger('syncro', {
            url: window.App.url(window.ServerController + '/borrar-filtro'),
            data: {},
            silent: false,
            callback: (response) => {
                console.log(response);
                if (response) {
                    const body = $('#filtro_add').find('tbody');
                    body.html('');
                    this.trigger('change:filtro', {});
                    return;
                }
            },
        });
    }

    removeFiltro(e) {
        e.preventDefault();
        this.$el.find(e.currentTarget).parent().parent().remove();
        this.trigger('change:filtro', {});
    }

    aplicaFiltro(e) {
        e.preventDefault();
        this.trigger('change:filtro', {});
    }

    __addFiltro() {
        let campo = $('#campo-filtro option:selected').text();
        let condi = $('#condi-filtro option:selected').text();
        let value = $('#value-filtro').val();

        let vcampo = $('#campo-filtro').val();
        let vcondi = $('#condi-filtro').val();

        if ($('#value-filtro').val() == '') return false;
        let _template = _.template(`
		<tr>
			<td>
				<%= campo %>
				<input id='mcampo-filtro[]' name='mcampo-filtro[]' type='hidden' value='<%=vcampo%>'/>
			</td>
			<td>
				<%=condi %>
				<input id='mcondi-filtro[]' name='mcondi-filtro[]'  type='hidden' value='<%=vcondi%>' />
			</td>
			<td>
				<%= value%>
				<input id='mvalue-filtro[]' name='mvalue-filtro[]' type='hidden' value='<%=value%>' />
			</td>
			<td>
				<button class='btn btn-outline-danger btn-sm' toggle-event='remove'>
					<span class='btn-inner--icon'><i class='fas fa-trash'></i></span>
				</button>
			</td>
		</tr>
		`);

        let html = $('#filtro_add').find('tbody');
        html.append(
            _template({
                campo,
                condi,
                value,
                vcampo,
                vcondi,
            }),
        );
    }

    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { FiltroView };
