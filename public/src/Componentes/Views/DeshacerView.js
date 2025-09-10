import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';

export default class DeshacerView extends ModelView {
	constructor(options) {
		super({...options, onRender: () => this.afterRender()});
		this.template = _.template(document.getElementById('tmp_deshacer').innerHTML);
	}

	get events(){
		return {
			'click #procesarDeshacer': 'deshacerSolicitud'
		}
	}

	afterRender(){
		this.$el.find('#nota_deshacer').summernote({
			lang: 'es-ES',
			placeholder: '',
			disableDragAndDrop: true,
			shortcuts: false,
			toolbar: [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['color', ['color']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['insert', ['link']],
				['para', ['ul', 'ol', 'paragraph']],
				['view', ['fullscreen', 'codeview']],
				['table', ['table']],
			],
			tabsize: 2,
			height: 100,
			callbacks: {
				onPaste: function (e) {
					e.preventDefault();
				},
				onKeyDown: function (e) {
					if (e.ctrlKey && e.keyCode === 86) {
						e.preventDefault();
					}
				},
			},
		});
	}

	deshacerSolicitud(e){
		e.preventDefault();
		this.trigger('run:deshacer', {
			data: {
				id: this.model.id,
				tipo: this.model.tipo,
				action: this.$el.find('#action').val(),
				codest: this.$el.find('#codest').val(),
				send_email: this.$el.find('#send_email').is(':checked')? 'S' : 'N',
				nota: this.$el.find('#nota_deshacer').summernote('code'),
			},
			callback: (response) => {
				if (response && response.success) {
					$App.trigger('alert:success', {
						message: 'Solicitud deshecha correctamente',
					});

					this.remove();
					$App.router.navigate('list', { trigger: true, replace: true });
				} else {
					$App.trigger('alert:error', {
						message: response.msj,
					})
				}
			}
		});
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}
