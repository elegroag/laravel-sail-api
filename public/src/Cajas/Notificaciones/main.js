import { $App } from '@/App';
import NotificacionesView from './NotificacionesView';
import Pagination from '@/Componentes/Views/CardPagination';

class Notificacion extends Backbone.Model {}

class Notificaciones extends Backbone.Collection {
	get model() {
		return Notificacion;
	}
}

$(() => {
	if($('#nav-notification').length) $('#nav-notification').remove();
	$App.initialize();

	const collectionNotificaciones = new Notificaciones();
	const pagination = new Pagination({
		endpoint: 'refresh_pagination',
		collection: collectionNotificaciones,
		App: $App,
	});
	$App.pagination = pagination;

	new NotificacionesView({
		el: '#boneLayout',
		collection: collectionNotificaciones,
		model: {}
	});

	pagination.cargarDatos({
		pagina: 1,
		filter: {},
		porPagina: 5
	});
});

