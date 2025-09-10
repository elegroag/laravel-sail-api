import { FiltroView } from '@/Componentes/Views/FiltroView';
import { ModelView } from '@/Common/ModelView';
import { Region } from '@/Common/Region';
import { $App } from '@/App';

class TableUsuariosView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.template = _.template($('#tmp_tabla_usuarios').html());
	}

	beforeRender() {
		this.filtro = new FiltroView();
		this.listenTo(this.filtro, 'change:filtro', this.__applyFiltro);
		this.$el.find('#filtro').html(this.filtro.render().el);
		this.__applyFiltro();
	}

	__applyFiltro() {
		const cantidad = this.$el.find('#cantidad_paginate').val();
		this.trigger('load:table', {
			tipo: this.collection.tipo,
			cantidad: cantidad,
			callback: (response) => {
				this.$el.find('#consulta').html(response.consulta);
				this.$el.find('#paginate').html(response.paginate);
				this.$el.find('#total_registros').text(response.total_registros);
			},
			silent: false,
		});
	}

	render() {
		this.$el.html(this.template());
		this.beforeRender();
		return this;
	}

	get events() {
		return {
			"click [data-toggle='info']": 'infoDetalle',
			"click [data-toggle='borrar']": 'borrarUsuario',
			"click [toggle-event='buscar']": 'buscarPagina',
			"change [toggle-event='change']": 'changeCantidad',
		};
	}

	buscarPagina(e) {
		e.preventDefault();
		const cantidad = this.$el.find('#cantidad_paginate').val();
		let pagina = parseInt($(e.currentTarget).find('a').text());

		if (isNaN(pagina) == true) pagina = parseInt($(e.currentTarget).attr('pagina'));
		if (pagina == 0) return;

		this.trigger('load:pagina', {
			cantidad: cantidad,
			pagina: pagina,
			tipo: this.collection.tipo,
			callback: (response) => {
				this.$el.find('#consulta').html(response.consulta);
				this.$el.find('#paginate').html(response.paginate);
			},
			silent: false,
		});
	}

	changeCantidad(e) {
		e.preventDefault();
		const cantidad = this.$el.find('#cantidad_paginate').val();
		let pagina = parseInt(this.$el.find('#paginate').find('a').text());

		if (isNaN(pagina) == true)
			pagina = parseInt(this.$el.find(e.currentTarget).attr('pagina'));
		if (pagina == 0) return;

		this.trigger('change:pagina', {
			cantidad: cantidad || 10,
			pagina: pagina,
			tipo: this.collection.tipo,
			callback: (response) => {
				this.$el.find('#consulta').html(response.consulta);
				this.$el.find('#paginate').html(response.paginate);
			},
			silent: false,
		});
	}

	infoDetalle(e) {
		e.preventDefault();
		const target = this.$el.find(e.currentTarget);
		const documento = target.attr('data-cid');
		const tipo = target.attr('data-tipo');
		const coddoc = target.attr('data-coddoc');
		$App.router.navigate('detalle/' + documento + '/' + tipo + '/' + coddoc, {
			trigger: true,
		});
	}

	borrarUsuario(e) {
		e.preventDefault();
		const target = this.$el.find(e.currentTarget);
		const documento = target.attr('data-cid');
		const tipo = target.attr('data-tipo');
		const coddoc = target.attr('data-coddoc');

		$App.trigger('confirma', {
			message:
				'Confirma que desea eliminar el usuario. Está acción borra todo registro asociado al usuario, como solicitudes de afiliación de trabajadores, beneficiarios, etc.',
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (status) {
					this.trigger('borrar:usuario', {
						data: {
							tipo: tipo,
							coddoc: coddoc,
							documento: documento,
						},
						callback: (response) => {
							if (response.success) {
								target.find('a').parent('tr').remove();
								this.__applyFiltro();

								$App.trigger('alert:success', {
									message: 'El usuario se ha eliminado exitosamente.',
								});
								$App.router.navigate('list', { trigger: true });
							} else {
								$App.trigger('alert:error', {
									message: 'Ocurrió un error al eliminar el usuario.',
								});
							}
						},
						silent: false,
					});
				}
			},
		});
	}
}

export { TableUsuariosView };
