import { $App } from '@/App';
import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { DateComponent, InputComponent, SelectComponent } from '@/Componentes/Views/ComponentsView';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';

class EditarSolicitudView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.template = _.template(document.getElementById('tmp_editar_solicitud').innerHTML);
		this.viewComponents = [];
		this.form = null;
	}

	get className() {
		return 'col';
	}

	get events() {
		return {
			'click #guardar_ficha': 'guardar',
		};
	}

	render() {
		const campos = this.collection.campos;
		const grupos = _.map(_.groupBy(campos, 'grupo'), (items, titulo) => ({ titulo, campos: items }));

		this.$el.html(this.template({ id: this.model.get('id'), grupos }));
		this.form = this.$el.find('#formEditarSolicitud');

		_.each(campos, (campo) => {
			const view = this.addComponent(new ComponentModel({ ...campo, valor: '' }));
			this.viewComponents.push(view);
			this.$el.find('#component_' + campo.name).html(view.$el);
		});

		this.actualizaForm(campos);

		this.form.validate({
			rules: this.reglas(campos),
			highlight: function (element) {
				$(element).removeClass('is-valid').addClass('is-invalid');
			},
			unhighlight: function (element) {
				$(element).removeClass('is-invalid').addClass('is-valid');
			},
		});
		return this;
	}

	onShow() {
		if ($.fn.select2) {
			this.$el.find('select').select2({ width: '100%' });
		}
		flatpickr(this.$el.find('.datepicker').toArray(), {
			locale: Spanish,
			dateFormat: 'Y-m-d',
			allowInput: true,
		});
	}

	addComponent(model) {
		switch (model.get('form_type')) {
			case 'select':
				return new SelectComponent({ model }).render();
			case 'date':
				return new DateComponent({ model }).render();
			default:
				return new InputComponent({ model }).render();
		}
	}

	actualizaForm(campos) {
		_.each(campos, (campo) => {
			let valor = this.model.get(campo.name);
			if (valor === null || valor === undefined || valor === '') return;
			valor = String(valor);
			if (campo.form_type === 'date') valor = valor.substring(0, 10);

			const input = this.$el.find(`[name="${campo.name}"]`);
			if (campo.form_type === 'select' && input.find('option').filter((i, opcion) => opcion.value === valor).length === 0) {
				input.append($('<option>').val(valor).text(valor));
			}
			input.val(valor);
		});
	}

	reglas(campos) {
		const reglas = {};
		_.each(campos, (campo) => {
			if (campo.type === 'email') reglas[campo.name] = { email: true };
			else if (campo.type === 'number') reglas[campo.name] = { number: true };
			else if (campo.form_type === 'date') reglas[campo.name] = { dateISO: true };
			else if (campo.form_type === 'input') reglas[campo.name] = { maxlength: 255 };
		});
		return reglas;
	}

	serializar() {
		const data = {};
		_.each(this.form.serializeArray(), (item) => {
			data[item.name] = item.value;
		});
		return data;
	}

	guardar(e) {
		e.preventDefault();
		const target = this.$el.find(e.currentTarget);

		if (this.form.valid() === false) {
			$App.trigger('alert:warning', { message: 'Se requiere de resolver los campos con error para continuar.' });
			return false;
		}

		$App.trigger('confirma', {
			message: 'Se requiere de confirmar que desea guardar los cambios en la información registrada de la solicitud.',
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (!status) return;
				target.attr('disabled', true);
				this.trigger('load:guardar', {
					data: this.serializar(),
					callback: (response) => {
						target.removeAttr('disabled');
						if (response && response.errors) {
							const errores = {};
							_.each(response.errors, (mensajes, campo) => {
								errores[campo] = _.isArray(mensajes) ? mensajes.join(' ') : mensajes;
							});
							this.form.validate().showErrors(errores);
						}
					},
				});
			},
		});
	}

	remove() {
		_.each(this.viewComponents, (view) => view.remove());
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { EditarSolicitudView };
