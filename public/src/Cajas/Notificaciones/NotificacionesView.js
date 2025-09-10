import { Region } from "@/Common/Region";
import NotificaDetailView from "./NotificaDetailView";
import {$App} from "@/App";

export default class NotificacionesView extends Backbone.View {
	#modalComponent = null;
	#viewDetail = null;
	#App = null;
	constructor(options={}) {
		super(options);
		this.#App = options.App || $App;
		this.template = _.template(document.getElementById('notificationRender').innerHTML);
		this.#modalComponent = new bootstrap.Modal(document.getElementById('modalComponent'));
	}

	initialize() {
		this.listenTo(this.collection, 'add', this.render);
		this.listenTo(this.collection, 'change', this.render);
		this.listenTo(this.collection, 'reset', this.render);
	}

	render() {
		this.$el.html(this.template());
		this.collection.forEach((model) => {
			if(model){
				const template = _.template(document.getElementById('itemNotification').innerHTML);
				this.$el.find('#notificationListarTodo').append(template(model.toJSON()));
			}
		});
		return this;
	}

	get events() {
		return {
			'click [data-toggle="detail-note"]': 'detailNote'
		}
	}

	#changeStateNoti(model={}) {
		this.#App.trigger('syncro', {
			url: this.#App.url('change_state'),
			silent: true,
			data: model.toJSON(),
			callback: (response={}) => {
				if(response && response.success){
					this.#App.trigger('noty:success', response.msj);
					$App.pagination.renderPagina();
				}else{
					this.#App.trigger('noty:error', response.msj);
				}
			}
		})
	}

	detailNote(e) {
		const id = e.currentTarget.dataset.id;
		if($('#mdl_set_footer')) $('#mdl_set_footer').remove();

		this.#modalComponent.show();
		const region = new Region({el: "#mdl_set_body"});

		$('#mdl_set_title').text('Detalle de la notificación');
		const model = this.collection.get(id);
		if(model.get('estado') === 'P')
		{
			model.set('estado', 'L');
			this.#changeStateNoti(model);
		}

		this.#viewDetail = new NotificaDetailView({model: model, App: this.#App});
		region.show(this.#viewDetail);
	}

}
