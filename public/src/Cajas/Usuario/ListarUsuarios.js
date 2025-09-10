import { $App } from '@/App';
import { UsuariosCollection } from './collections/UsuariosCollection';
import { LayoutUsuario } from './views/LayoutUsuario';
import { TableUsuariosView } from './views/TableUsuariosView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderListView } from '@/Cajas/HeaderListView';

export default class ListarUsuarios {
	constructor(options) {
		this.region = options.region;
		_.extend(this, Backbone.Events);
		$App.Collections.usuarios = new UsuariosCollection();
	}

	listarUsuarios(tipo = '') {
		this.layout = new LayoutUsuario();
		this.region.show(this.layout);

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Usuarios Externos',
				detalle: 'Listado de usuarios externos',
				info: true,
			},
		});

		this.listenTo(this.headerMain, 'show:filtro', this.__showFiltro);

		this.layout.getRegion('header').show(this.headerMain);

		this.layout.getRegion('subheader').show(
			new HeaderListView({
				model: {
					tipo: tipo,
				},
			}),
		);
		const view = new TableUsuariosView({ collection: { tipo: tipo } });

		this.listenTo(view, 'load:table', this.__aplicarFiltro);
		this.listenTo(view, 'load:pagina', this.__buscarPagina);
		this.listenTo(view, 'change:pagina', this.__cambiarPagina);
		this.listenTo(view, 'borrar:usuario', this.__borrarUsuario);

		this.layout.getRegion('body').show(view);
	}

	__aplicarFiltro(transfer = {}) {
		const { callback = void 0, cantidad = 10, tipo = '' } = transfer;
		const url =
			tipo !== '' && tipo !== null && tipo !== undefined
				? 'aplicarFiltro/' + tipo
				: 'aplicarFiltro';

		$App.trigger('syncro', {
			url: url,
			data: {
				campo: $("input[type='hidden'][name='mcampo-filtro[]']").serialize(),
				condi: $("input[type='hidden'][name='mcondi-filtro[]']").serialize(),
				value: $("input[type='hidden'][name='mvalue-filtro[]']").serialize(),
				numero: cantidad,
			},
			callback: (response) => {
				return callback(response);
			},
		});
	}

	__borrarUsuario(transfer = {}) {
		const { callback = void 0, data = void 0 } = transfer;
		const url = 'borrarUsuario';
		$App.trigger('syncro', {
			url: url,
			data: data,
			callback: (response) => {
				return callback(response);
			},
		});
	}

	__showFiltro() {
		const myModal = new bootstrap.Modal('#filtrar-modal', {
			keyboard: false,
		});
		myModal.show();
	}

	__cambiarPagina(transfer) {
		const { callback = void 0, cantidad = 10, pagina = 1, tipo = '' } = transfer;
		const url =
			tipo !== '' && tipo !== null && tipo !== undefined
				? 'changeCantidadPagina/' + tipo
				: 'changeCantidadPagina';

		$App.trigger('syncro', {
			url: url,
			data: {
				pagina: pagina,
				numero: cantidad,
			},
			callback: (response) => {
				return callback(response);
			},
		});
	}

	__buscarPagina(transfer) {
		const { callback = void 0, cantidad = 10, pagina = 0, tipo = '' } = transfer;
		const url =
			tipo !== '' && tipo !== null && tipo !== undefined ? 'buscar/' + tipo : 'buscar';
		$App.trigger('syncro', {
			url: url,
			data: {
				pagina: pagina,
				numero: cantidad,
			},
			callback: (response) => {
				return callback ? callback(response) : false;
			},
		});
	}

	destroy() {
		this.region.remove();
		// @ts-ignore
		this.stopListening();
	}
}
